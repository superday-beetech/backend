<?php

declare(strict_types=1);

namespace Modules\Pharmacy\Controllers\API\V1;

use App\Core\ApiController;
use Modules\Pharmacy\Models\Master\ProductModel;
use Modules\Pharmacy\Models\Master\CategoryModel;
use Modules\Pharmacy\Models\Master\UnitModel;
use Throwable;

class ProductApiController extends ApiController
{
    private ProductModel $productModel;
    private CategoryModel $categoryModel;
    private UnitModel $unitModel;

    public function initController($request, $response, $logger)
    {
        parent::initController($request, $response, $logger);

        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->unitModel = new UnitModel();
    }


    public function index()
    {
        try {
            $page = max(1, (int) ($this->request->getGet('page') ?? 1));

            $perPage = (int) ($this->request->getGet('per_page') ?? 10);
            $perPage = max(1, min($perPage, 100));

            $search = trim((string) ($this->request->getGet('search') ?? ''));

            $categoryId = (int) ($this->request->getGet('category_id') ?? 0);

            $isActive = $this->request->getGet('is_active');

            $dateFrom = trim((string) ($this->request->getGet('date_from') ?? ''));
            $dateTo = trim((string) ($this->request->getGet('date_to') ?? ''));

            // Validasi format tanggal YYYY-MM-DD.
            $validateDate = static function (string $date): bool {
                $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);

                return $parsed !== false && $parsed->format('Y-m-d') === $date;
            };

            if (
                ($dateFrom !== '' && !$validateDate($dateFrom)) ||
                ($dateTo !== '' && !$validateDate($dateTo))
            ) {
                return $this->errorResponse('Format tanggal tidak valid.', 400);
            }

            if ($dateFrom !== '' && $dateTo !== '' && $dateFrom > $dateTo) {
                return $this->errorResponse(
                    'Tanggal awal tidak boleh melebihi tanggal akhir.',
                    400
                );
            }

            // Validasi status: hanya 0 atau 1.
            if (
                $isActive !== null &&
                $isActive !== '' &&
                !in_array((string) $isActive, ['0', '1'], true)
            ) {
                return $this->errorResponse('Status produk tidak valid.', 400);
            }

            // Terapkan filter yang sama pada query total dan query data.
            $applyFilters = static function ($builder) use (
                $search,
                $categoryId,
                $isActive,
                $dateFrom,
                $dateTo
            ): void {
                if ($search !== '') {
                    $builder->groupStart()
                        ->like('products.name', $search)
                        ->orLike('products.sku', $search)
                        ->orLike('products.barcode', $search)
                        ->groupEnd();
                }

                if ($categoryId > 0) {
                    $builder->where('products.category_id', $categoryId);
                }

                if ($isActive !== null && $isActive !== '') {
                    $builder->where('products.is_active', (int) $isActive);
                }

                // Filter berdasarkan tanggal produk dibuat.
                if ($dateFrom !== '') {
                    $builder->where(
                        'products.created_at >=',
                        $dateFrom . ' 00:00:00'
                    );
                }

                if ($dateTo !== '') {
                    $builder->where(
                        'products.created_at <=',
                        $dateTo . ' 23:59:59'
                    );
                }
            };

            // Hitung total data setelah seluruh filter diterapkan.
            $countBuilder = $this->productModel->builder();

            $applyFilters($countBuilder);

            $total = $countBuilder->countAllResults();

            // Ambil data beserta relasi kategori dan satuan.
            $builder = $this->productModel
                ->select('
                    products.*,
                    categories.name AS category_name,
                    units.name AS unit_name,
                    units.abbreviation AS unit_abbreviation,
                    d.generic_name,
                    d.active_ingredient,
                    d.dosage_form,
                    d.strength,
                    d.drug_class,
                    d.bpom_number,
                    d.requires_prescription,
                    d.unit_conversion,
                    d.min_stock,
                    d.max_stock,
                    d.rack_location,
                    d.manufacturer
                ')
                ->join('categories', 'categories.id = products.category_id', 'left')
                ->join('units', 'units.id = products.unit_id', 'left')
                ->join('product_details d', 'd.product_id = products.id', 'left');

            $applyFilters($builder);

            $items = $builder
                ->orderBy('products.name', 'ASC')
                ->findAll($perPage, ($page - 1) * $perPage);

            $totalPages = (int) ceil($total / $perPage);

            return $this->successResponse([
                'items' => $items,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'total_pages' => $totalPages,
                ],
            ], 'Data produk berhasil diambil.');
        } catch (Throwable $e) {
            log_message(
                'error',
                '[ProductApiController::index] ' . $e->getMessage()
            );

            return $this->errorResponse(
                'Gagal mengambil data produk.',
                500
            );
        }
    }


    public function show($id = null)
    {
        try {
            $id = (int) $id;

            if ($id <= 0) {
                return $this->errorResponse('ID produk tidak valid.', 400);
            }

            $product = $this->productModel
                ->select('products.*, categories.name AS category_name, units.name AS unit_name, units.abbreviation AS unit_abbreviation')
                ->join('categories', 'categories.id = products.category_id', 'left')
                ->join('units', 'units.id = products.unit_id', 'left')
                ->where('products.id', $id)
                ->first();

            if (!$product) {
                return $this->errorResponse('Produk tidak ditemukan.', 404);
            }

            return $this->successResponse($product, 'Detail produk berhasil diambil.');
        } catch (Throwable $e) {
            log_message('error', '[ProductApiController::show] ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil detail produk.', 500);
        }
    }

    public function createWithDetail()
    {
        try {
            $data = $this->request->getJSON(true);

            if (!is_array($data)) {
                return $this->errorResponse('Format request tidak valid.', 400);
            }

            // Validasi produk inti (sama seperti create)
            $rules = [
                'sku'             => 'required|min_length[1]|max_length[50]',
                'name'            => 'required|min_length[2]|max_length[150]',
                'category_id'     => 'required|is_natural_no_zero',
                'unit_id'         => 'required|is_natural_no_zero',
                'purchase_price'  => 'required|numeric|greater_than_equal_to[0]',
                'selling_price'   => 'required|numeric|greater_than_equal_to[0]',
            ];

            if (!$this->validateData($data, $rules)) {
                return $this->errorResponse('Validasi produk gagal.', 422, $this->validator->getErrors());
            }

            $userId = (int) auth()->id();

            $db = \Config\Database::connect();
            $db->transBegin();

            try {
                // 1. Insert produk
                $productId = $this->productModel->insert([
                    'sku'            => trim($data['sku']),
                    'barcode'        => $data['barcode'] ?? null,
                    'name'           => trim($data['name']),
                    'category_id'    => (int) $data['category_id'],
                    'unit_id'        => (int) $data['unit_id'],
                    'purchase_price' => (float) $data['purchase_price'],
                    'selling_price'  => (float) $data['selling_price'],
                    'description'    => $data['description'] ?? null,
                    'is_active'      => (int) ($data['is_active'] ?? 1),
                    'created_by'     => $userId,
                    'updated_by'     => $userId,
                ]);

                if (!$productId) {
                    throw new \RuntimeException('Gagal menyimpan produk.');
                }

                // 2. Insert product_details (jika ada)
                if (!empty($data['detail']) && is_array($data['detail'])) {
                    $detailModel = new \Modules\Pharmacy\Models\Master\ProductDetailModel();
                    $detailModel->insert([
                        'product_id'            => $productId,
                        'generic_name'          => $data['detail']['generic_name'] ?? null,
                        'active_ingredient'     => $data['detail']['active_ingredient'] ?? null,
                        'dosage_form'           => $data['detail']['dosage_form'] ?? null,
                        'strength'              => $data['detail']['strength'] ?? null,
                        'drug_class'            => $data['detail']['drug_class'] ?? null,
                        'bpom_number'           => $data['detail']['bpom_number'] ?? null,
                        'requires_prescription' => (int) ($data['detail']['requires_prescription'] ?? 0),
                        'min_stock'             => (int) ($data['detail']['min_stock'] ?? 0),
                        'max_stock'             => $data['detail']['max_stock'] ?? null,
                        'rack_location'         => $data['detail']['rack_location'] ?? null,
                        'manufacturer'          => $data['detail']['manufacturer'] ?? null,
                    ]);
                }

                // 3. Sync product_attributes (unit, image, info)
                $attrService = new \Modules\Pharmacy\Services\ProductAttributeService();

                if (!empty($data['units']) && is_array($data['units'])) {
                    $attrService->syncUnits($productId, $data['units'], $userId);
                }
                if (!empty($data['images']) && is_array($data['images'])) {
                    $attrService->syncImages($productId, $data['images'], $userId);
                }
                if (!empty($data['infos']) && is_array($data['infos'])) {
                    $attrService->syncInfos($productId, $data['infos'], $userId);
                }

                $db->transCommit();

                return $this->successResponse(
                    $this->productModel->findWithDetails($productId),
                    'Produk berhasil ditambahkan.',
                    201
                );
            } catch (\Throwable $e) {
                $db->transRollback();
                throw $e;
            }
        } catch (Throwable $e) {
            log_message('error', '[ProductApiController::createWithDetail] ' . $e->getMessage());
            return $this->errorResponse('Gagal menambahkan produk.', 500);
        }
    }

    // ProductApiController::attributes($id)
    public function attributes($id = null)
    {
        try {
            $id = (int) $id;
            if ($id <= 0) {
                return $this->errorResponse('ID produk tidak valid.', 400);
            }
            if (!$this->productModel->find($id)) {
                return $this->errorResponse('Produk tidak ditemukan.', 404);
            }

            $service = new \Modules\Pharmacy\Services\ProductAttributeService();
            $grouped = $service->getGrouped($id);

            return $this->successResponse($grouped, 'Atribut produk berhasil diambil.');
        } catch (Throwable $e) {
            log_message('error', '[ProductApiController::attributes] ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil atribut produk.', 500);
        }
    }

    public function updateWithDetail($id = null)
    {
        try {
            $id = (int) $id;
            if ($id <= 0) {
                return $this->errorResponse('ID produk tidak valid.', 400);
            }

            if (!$this->productModel->find($id)) {
                return $this->errorResponse('Produk tidak ditemukan.', 404);
            }

            $data = $this->request->getJSON(true);
            if (!is_array($data) || !$data) {
                return $this->errorResponse('Data perubahan produk wajib diisi.', 400);
            }

            // Validasi (sama seperti update)
            $rules = [
                'sku'            => 'permit_empty|min_length[1]|max_length[50]',
                'barcode'        => 'permit_empty|max_length[100]',
                'name'           => 'permit_empty|min_length[2]|max_length[150]',
                'category_id'    => 'permit_empty|is_natural_no_zero',
                'unit_id'        => 'permit_empty|is_natural_no_zero',
                'purchase_price' => 'permit_empty|numeric|greater_than_equal_to[0]',
                'selling_price'  => 'permit_empty|numeric|greater_than_equal_to[0]',
                'is_active'      => 'permit_empty|in_list[0,1]',
            ];

            if (!$this->validateData($data, $rules)) {
                return $this->errorResponse('Validasi perubahan produk gagal.', 422, $this->validator->getErrors());
            }

            $userId = (int) auth()->id();

            $db = \Config\Database::connect();
            $db->transBegin();

            try {
                // 1. Update produk inti (hanya field yang dikirim)
                $update = [];
                foreach (['sku','barcode','name','category_id','unit_id','purchase_price','selling_price','description','is_active'] as $f) {
                    if (array_key_exists($f, $data)) {
                        $update[$f] = $data[$f];
                    }
                }

                if (isset($update['sku'])) {
                    $update['sku'] = trim((string) $update['sku']);
                    $dup = $this->productModel->where('sku', $update['sku'])->where('id !=', $id)->first();
                    if ($dup) {
                        throw new \RuntimeException('SKU produk sudah digunakan.');
                    }
                }

                if (array_key_exists('barcode', $update)) {
                    $update['barcode'] = trim((string) $update['barcode']);
                    if ($update['barcode'] === '') {
                        $update['barcode'] = null;
                    } else {
                        $dup = $this->productModel->where('barcode', $update['barcode'])->where('id !=', $id)->first();
                        if ($dup) {
                            throw new \RuntimeException('Barcode produk sudah digunakan.');
                        }
                    }
                }

                if ($update) {
                    $update['updated_by'] = $userId;
                    $this->productModel->update($id, $update);
                }

                // 2. Upsert product_details
                $detailModel = new \Modules\Pharmacy\Models\Master\ProductDetailModel();
                $existingDetail = $detailModel->where('product_id', $id)->first();

                if (!empty($data['detail']) && is_array($data['detail'])) {
                    $detailFields = [
                        'generic_name'          => $data['detail']['generic_name'] ?? null,
                        'active_ingredient'     => $data['detail']['active_ingredient'] ?? null,
                        'dosage_form'           => $data['detail']['dosage_form'] ?? null,
                        'strength'              => $data['detail']['strength'] ?? null,
                        'drug_class'            => $data['detail']['drug_class'] ?? null,
                        'bpom_number'           => $data['detail']['bpom_number'] ?? null,
                        'requires_prescription' => (int) ($data['detail']['requires_prescription'] ?? 0),
                        'min_stock'             => (int) ($data['detail']['min_stock'] ?? 0),
                        'max_stock'             => $data['detail']['max_stock'] ?? null,
                        'rack_location'         => $data['detail']['rack_location'] ?? null,
                        'manufacturer'          => $data['detail']['manufacturer'] ?? null,
                    ];

                    if ($existingDetail) {
                        $detailModel->update($existingDetail['id'], $detailFields);
                    } else {
                        $detailFields['product_id'] = $id;
                        $detailModel->insert($detailFields);
                    }
                }

                // 3. Sync product_attributes (replace all per tipe)
                $attrService = new \Modules\Pharmacy\Services\ProductAttributeService();

                if (array_key_exists('units', $data) && is_array($data['units'])) {
                    $attrService->syncUnits($id, $data['units'], $userId);
                }
                if (array_key_exists('images', $data) && is_array($data['images'])) {
                    $attrService->syncImages($id, $data['images'], $userId);
                }
                if (array_key_exists('infos', $data) && is_array($data['infos'])) {
                    $attrService->syncInfos($id, $data['infos'], $userId);
                }

                $db->transCommit();

                return $this->successResponse(
                    $this->productModel->findWithDetails($id),
                    'Produk berhasil diperbarui.'
                );
            } catch (\Throwable $e) {
                $db->transRollback();
                throw $e;
            }
        } catch (Throwable $e) {
            log_message('error', '[ProductApiController::updateWithDetail] ' . $e->getMessage());
            return $this->errorResponse($e->getMessage() ?: 'Gagal memperbarui produk.', 500);
        }
    }
    public function uploadImage()
    {
        try {
            $file = $this->request->getFile('image');

            if (!$file || !$file->isValid()) {
                return $this->errorResponse('File gambar tidak valid.', 400);
            }

            if ($file->hasMoved()) {
                return $this->errorResponse('File sudah dipindahkan.', 400);
            }

            // Maks 2MB
            if ($file->getSize() > 2 * 1024 * 1024) {
                return $this->errorResponse('Ukuran gambar maksimal 2MB.', 422);
            }

            // Validasi MIME (deteksi dari file, bukan client)
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
            $mime = $file->getMimeType();
            if (!in_array($mime, $allowedMimes, true)) {
                return $this->errorResponse('Format gambar harus JPG, PNG, atau WEBP.', 422);
            }

            // Pastikan folder ada
            $uploadPath = WRITEPATH . 'uploads/products/temp';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0775, true);
            }

            // Rename acak
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);

            $url = '/uploads/products/temp/' . $newName;

            return $this->successResponse([
                'url'      => $url,
                'filename' => $newName,
            ], 'Gambar berhasil diunggah.', 201);
        } catch (Throwable $e) {
            log_message('error', '[ProductApiController::uploadImage] ' . $e->getMessage());
            return $this->errorResponse('Gagal mengunggah gambar.', 500);
        }
    }

    public function deleteImage()
    {
        try {
            $data = $this->request->getJSON(true);
            $filename = basename($data['filename'] ?? '');

            if (!$filename) {
                return $this->errorResponse('Nama file tidak valid.', 400);
            }

            $path = WRITEPATH . 'uploads/products/temp/' . $filename;
            if (is_file($path)) {
                unlink($path);
            }

            return $this->successResponse(null, 'Gambar dibatalkan.');
        } catch (Throwable $e) {
            return $this->errorResponse('Gagal menghapus gambar.', 500);
        }
    }

    public function create()
    {
        try {
            $data = $this->request->getJSON(true);

            if (!is_array($data)) {
                return $this->errorResponse('Format request tidak valid.', 400);
            }

            $rules = [
                'sku' => 'required|min_length[1]|max_length[50]',
                'barcode' => 'permit_empty|max_length[100]',
                'name' => 'required|min_length[2]|max_length[150]',
                'category_id' => 'required|is_natural_no_zero',
                'unit_id' => 'required|is_natural_no_zero',
                'purchase_price' => 'required|numeric|greater_than_equal_to[0]',
                'selling_price' => 'required|numeric|greater_than_equal_to[0]',
                'description' => 'permit_empty|max_length[2000]',
                'is_active' => 'permit_empty|in_list[0,1]',
            ];

            if (!$this->validateData($data, $rules)) {
                return $this->errorResponse(
                    'Validasi produk gagal.',
                    422,
                    $this->validator->getErrors()
                );
            }

            if (!$this->categoryModel->find((int) $data['category_id'])) {
                return $this->errorResponse('Kategori produk tidak ditemukan.', 422);
            }

            if (!$this->unitModel->find((int) $data['unit_id'])) {
                return $this->errorResponse('Satuan produk tidak ditemukan.', 422);
            }

            $sku = trim($data['sku']);
            $barcode = trim((string) ($data['barcode'] ?? ''));

            if ($this->productModel->where('sku', $sku)->first()) {
                return $this->errorResponse('SKU produk sudah digunakan.', 409);
            }

            if (
                $barcode !== '' &&
                $this->productModel->where('barcode', $barcode)->first()
            ) {
                return $this->errorResponse('Barcode produk sudah digunakan.', 409);
            }

            $userId = (int) auth()->id();

            $id = $this->productModel->insert([
                'sku' => $sku,
                'barcode' => $barcode !== '' ? $barcode : null,
                'name' => trim($data['name']),
                'category_id' => (int) $data['category_id'],
                'unit_id' => (int) $data['unit_id'],
                'purchase_price' => $data['purchase_price'],
                'selling_price' => $data['selling_price'],
                'description' => $data['description'] ?? null,
                'is_active' => isset($data['is_active'])
                    ? (int) $data['is_active']
                    : 1,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            if (!$id) {
                return $this->errorResponse(
                    'Gagal menyimpan produk.',
                    500,
                    $this->productModel->errors()
                );
            }

            return $this->successResponse(
                $this->productModel->find($id),
                'Produk berhasil ditambahkan.',
                201
            );
        } catch (Throwable $e) {
            log_message('error', '[ProductApiController::create] ' . $e->getMessage());
            return $this->errorResponse('Terjadi kesalahan saat menambahkan produk.', 500);
        }
    }

    public function update($id = null)
    {
        try {
            $id = (int) $id;

            if ($id <= 0) {
                return $this->errorResponse('ID produk tidak valid.', 400);
            }

            if (!$this->productModel->find($id)) {
                return $this->errorResponse('Produk tidak ditemukan.', 404);
            }

            $data = $this->request->getJSON(true);

            if (!is_array($data) || !$data) {
                return $this->errorResponse('Data perubahan produk wajib diisi.', 400);
            }

            $rules = [
                'id' => 'permit_empty|is_natural_no_zero',
                'sku' => 'permit_empty|min_length[1]|max_length[50]',
                'barcode' => 'permit_empty|max_length[100]',
                'name' => 'permit_empty|min_length[2]|max_length[150]',
                'category_id' => 'permit_empty|is_natural_no_zero',
                'unit_id' => 'permit_empty|is_natural_no_zero',
                'purchase_price' => 'permit_empty|numeric|greater_than_equal_to[0]',
                'selling_price' => 'permit_empty|numeric|greater_than_equal_to[0]',
                'description' => 'permit_empty|max_length[2000]',
                'is_active' => 'permit_empty|in_list[0,1]',
            ];

            if (!$this->validateData($data, $rules)) {
                return $this->errorResponse(
                    'Validasi perubahan produk gagal.',
                    422,
                    $this->validator->getErrors()
                );
            }

            $current = $this->productModel->find($id);
            $update = [];

            foreach (
                [
                    'sku',
                    'barcode',
                    'name',
                    'category_id',
                    'unit_id',
                    'purchase_price',
                    'selling_price',
                    'description',
                    'is_active',
                ] as $field
            ) {
                if (array_key_exists($field, $data)) {
                    $update[$field] = $data[$field];
                }
            }

            if (isset($update['sku'])) {
                $update['sku'] = trim((string) $update['sku']);

                $duplicate = $this->productModel
                    ->where('sku', $update['sku'])
                    ->where('id !=', $id)
                    ->first();

                if ($duplicate) {
                    return $this->errorResponse('SKU produk sudah digunakan.', 409);
                }
            }

            if (array_key_exists('barcode', $update)) {
                $update['barcode'] = trim((string) $update['barcode']);

                if ($update['barcode'] === '') {
                    $update['barcode'] = null;
                } else {
                    $duplicate = $this->productModel
                        ->where('barcode', $update['barcode'])
                        ->where('id !=', $id)
                        ->first();

                    if ($duplicate) {
                        return $this->errorResponse('Barcode produk sudah digunakan.', 409);
                    }
                }
            }

            if (
                isset($update['category_id']) &&
                !$this->categoryModel->find((int) $update['category_id'])
            ) {
                return $this->errorResponse('Kategori produk tidak ditemukan.', 422);
            }

            if (
                isset($update['unit_id']) &&
                !$this->unitModel->find((int) $update['unit_id'])
            ) {
                return $this->errorResponse('Satuan produk tidak ditemukan.', 422);
            }

            if (!$update) {
                return $this->errorResponse('Tidak ada data yang diperbarui.', 400);
            }

            $update['updated_by'] = (int) auth()->id();
            $update['id'] = $id;
            if (!$this->productModel->update($id, $update)) {
                return $this->errorResponse(
                    'Gagal memperbarui produk.',
                    500,
                    $this->productModel->errors()
                );
            }

            return $this->successResponse(
                $this->productModel->find($id),
                'Produk berhasil diperbarui.'
            );
        } catch (Throwable $e) {
            log_message('error', '[ProductApiController::update] ' . $e->getMessage());
            return $this->errorResponse('Terjadi kesalahan saat memperbarui produk.', 500);
        }
    }

    public function delete($id = null)
    {
        try {
            $id = (int) $id;

            if ($id <= 0) {
                return $this->errorResponse('ID produk tidak valid.', 400);
            }

            if (!$this->productModel->find($id)) {
                return $this->errorResponse('Produk tidak ditemukan.', 404);
            }

            if (!$this->productModel->delete($id)) {
                return $this->errorResponse('Gagal menghapus produk.', 500);
            }

            return $this->successResponse(['id' => $id], 'Produk berhasil dihapus.');
        } catch (Throwable $e) {
            log_message('error', '[ProductApiController::delete] ' . $e->getMessage());
            return $this->errorResponse('Terjadi kesalahan saat menghapus produk.', 500);
        }
    }
}
