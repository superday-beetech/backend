<?php

declare(strict_types=1);

namespace Modules\\Pharmacy\\Tests\\Feature;

use CodeIgniter\\Test\\CIUnitTestCase;
use Config\\Database;
use Modules\\Pharmacy\\Services\\InventoryService;
use Modules\\Pharmacy\\Services\\ReturnsService;
use Modules\\Pharmacy\\Services\\SalesService;
use RuntimeException;
use Throwable;

/**
 * Integration tests for the highest-risk stock transitions.
 *
 * Configure the "pharmacy" DB group to an isolated test database before
 * running php spark test. The outer transaction is rolled back after each test.
 */
final class InventoryPosVerticalSliceTest extends CIUnitTestCase
{
    private $db;
    private int $userId = 1;
    private int $productId;
    private int $purchaseOrderId;
    private int $purchaseOrderItemId;
    private int $receiptId;
    private int $receiptItemId;

    protected function setUp(): void
    {
        parent::setUp();

        if (ENVIRONMENT !== 'testing') {
            $this->markTestSkipped('Test stok hanya boleh dijalankan pada environment testing.');
        }

        $this->db = Database::connect('pharmacy');
        $this->db->transBegin();

        $now = date('Y-m-d H:i:s');
        $suffix = strtoupper(bin2hex(random_bytes(4)));

        $this->db->table('categories')->insert([
            'name' => 'VS Category ' . $suffix,
            'description' => 'Vertical slice test',
            'is_active' => 1,
        ]);
        $categoryId = (int) $this->db->insertID();

        $this->db->table('units')->insert([
            'name' => 'Unit VS ' . $suffix,
            'abbreviation' => 'UVS' . $suffix,
        ]);
        $unitId = (int) $this->db->insertID();

        $this->db->table('products')->insert([
            'sku' => 'VS-' . $suffix,
            'barcode' => null,
            'name' => 'Vertical Slice Product ' . $suffix,
            'category_id' => $categoryId,
            'unit_id' => $unitId,
            'purchase_price' => 40,
            'selling_price' => 100,
            'is_active' => 1,
            'created_by' => $this->userId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->productId = (int) $this->db->insertID();

        $this->db->table('suppliers')->insert([
            'code' => 'VS-' . $suffix,
            'name' => 'Vertical Slice Supplier ' . $suffix,
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $supplierId = (int) $this->db->insertID();

        $this->db->table('purchase_orders')->insert([
            'order_number' => 'VS-PO-' . $suffix,
            'supplier_id' => $supplierId,
            'order_date' => date('Y-m-d'),
            'status' => 'ordered',
            'subtotal' => 400,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'total_amount' => 400,
            'created_by' => $this->userId,
            'updated_by' => $this->userId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->purchaseOrderId = (int) $this->db->insertID();

        $this->db->table('purchase_order_items')->insert([
            'purchase_order_id' => $this->purchaseOrderId,
            'product_id' => $this->productId,
            'quantity' => 10,
            'unit_cost' => 40,
            'discount_amount' => 0,
            'subtotal' => 400,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->purchaseOrderItemId = (int) $this->db->insertID();

        $this->db->table('goods_receipts')->insert([
            'receipt_number' => 'VS-GR-' . $suffix,
            'purchase_order_id' => $this->purchaseOrderId,
            'received_date' => date('Y-m-d'),
            'status' => 'draft',
            'created_by' => $this->userId,
            'updated_by' => $this->userId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->receiptId = (int) $this->db->insertID();

        $this->db->table('goods_receipt_items')->insert([
            'goods_receipt_id' => $this->receiptId,
            'purchase_order_item_id' => $this->purchaseOrderItemId,
            'product_id' => $this->productId,
            'quantity_received' => 10,
            'unit_cost' => 40,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->receiptItemId = (int) $this->db->insertID();
    }

    protected function tearDown(): void
    {
        if ($this->db && $this->db->transDepth > 0) {
            $this->db->transRollback();
        }

        parent::tearDown();
    }

    public function testGoodsReceiptPostingAddsStockExactlyOnce(): void
    {
        $service = new InventoryService();
        $service->postGoodsReceipt($this->receiptId, [
            $this->receiptItemId => [[
                'batch_number' => 'VS-BATCH-001',
                'expired_at' => '2027-12-31',
                'quantity' => 10,
            ]],
        ], $this->userId);

        $batch = $this->db->table('product_batches')
            ->where('goods_receipt_item_id', $this->receiptItemId)
            ->get()->getRowArray();

        $this->assertNotEmpty($batch);
        $this->assertSame('10.000', number_format((float) $batch['quantity_remaining'], 3, '.', ''));
        $this->assertSame('posted', $this->db->table('goods_receipts')
            ->where('id', $this->receiptId)->get()->getRow('status'));

        $this->expectException(RuntimeException::class);
        $service->postGoodsReceipt($this->receiptId, [
            $this->receiptItemId => [[
                'batch_number' => 'VS-BATCH-001',
                'expired_at' => '2027-12-31',
                'quantity' => 10,
            ]],
        ], $this->userId);
    }

    public function testPosCheckoutDeductsStockAndRepeatedKeyDoesNotDeductTwice(): void
    {
        $this->postFixtureReceipt();

        $service = new SalesService();
        $payload = [
            'sale_date' => date('Y-m-d H:i:s'),
            'items' => [[
                'product_id' => $this->productId,
                'quantity' => 3,
                'unit_price' => 100,
                'discount_amount' => 0,
            ]],
        ];
        $payment = [
            'settlement_mode' => 'paid',
            'amount' => 300,
            'applied_amount' => 300,
            'amount_received' => 300,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'reference_number' => null,
            'due_date' => null,
        ];

        $saleId = $service->checkout($payload, $payment, $this->userId, 'vs-pos-checkout-key');
        $repeatSaleId = $service->checkout($payload, $payment, $this->userId, 'vs-pos-checkout-key');

        $this->assertSame($saleId, $repeatSaleId);
        $this->assertSame(7.0, (float) $this->db->table('product_batches')
            ->where('product_id', $this->productId)
            ->selectSum('quantity_remaining', 'balance')
            ->get()->getRow()->balance);

        $movementCount = $this->db->table('stock_movements')
            ->where('reference_type', 'sale')
            ->where('reference_id', $saleId)
            ->countAllResults();
        $this->assertSame(1, $movementCount);
    }

    public function testPosSalesReturnRestocksBatchOnlyOnce(): void
    {
        $this->postFixtureReceipt();

        $sales = new SalesService();
        $saleId = $sales->checkout([
            'sale_date' => date('Y-m-d H:i:s'),
            'items' => [[
                'product_id' => $this->productId,
                'quantity' => 3,
                'unit_price' => 100,
                'discount_amount' => 0,
            ]],
        ], [
            'settlement_mode' => 'paid',
            'amount' => 300,
            'applied_amount' => 300,
            'amount_received' => 300,
            'change_amount' => 0,
            'payment_method' => 'cash',
        ], $this->userId, 'vs-pos-return-key');

        $saleItem = $this->db->table('sale_items')
            ->where('sale_id', $saleId)
            ->get()->getRowArray();

        $returns = new ReturnsService();
        $returnId = $returns->createSalesReturn([
            'return_number' => 'VS-POS-RET-' . strtoupper(bin2hex(random_bytes(3))),
            'sale_id' => $saleId,
            'return_date' => date('Y-m-d'),
            'items' => [[
                'sale_item_id' => (int) $saleItem['id'],
                'quantity' => 2,
                'restockable' => true,
                'condition_notes' => 'Kemasan baik',
            ]],
        ], $this->userId);

        $result = $returns->postSalesReturn($returnId, $this->userId);
        $this->assertSame('posted', $result['status']);
        $this->assertSame(2.0, (float) $result['restocked_quantity']);

        $balance = (float) $this->db->table('product_batches')
            ->where('product_id', $this->productId)
            ->selectSum('quantity_remaining', 'balance')
            ->get()->getRow()->balance;
        $this->assertSame(9.0, $balance);

        $this->expectException(RuntimeException::class);
        $returns->postSalesReturn($returnId, $this->userId);
    }

    public function testCheckoutWithInsufficientStockRollsBackSaleAndMovements(): void
    {
        $this->postFixtureReceipt();

        $service = new SalesService();
        try {
            $service->checkout([
                'sale_date' => date('Y-m-d H:i:s'),
                'items' => [[
                    'product_id' => $this->productId,
                    'quantity' => 11,
                    'unit_price' => 100,
                    'discount_amount' => 0,
                ]],
            ], [
                'settlement_mode' => 'paid',
                'amount' => 1100,
                'applied_amount' => 1100,
                'amount_received' => 1100,
                'change_amount' => 0,
                'payment_method' => 'cash',
            ], $this->userId, 'vs-pos-insufficient-key');
            $this->fail('Checkout seharusnya gagal karena stok tidak cukup.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('Stok produk', $e->getMessage());
        }

        $this->assertSame(0, $this->db->table('sales')
            ->where('status', 'posted')->countAllResults());
        $this->assertSame(0, $this->db->table('stock_movements')
            ->where('reference_type', 'sale')->countAllResults());
        $this->assertSame(10.0, (float) $this->db->table('product_batches')
            ->where('product_id', $this->productId)
            ->selectSum('quantity_remaining', 'balance')
            ->get()->getRow()->balance);
    }

    private function postFixtureReceipt(): void
    {
        (new InventoryService())->postGoodsReceipt($this->receiptId, [
            $this->receiptItemId => [[
                'batch_number' => 'VS-BATCH-001',
                'expired_at' => '2027-12-31',
                'quantity' => 10,
            ]],
        ], $this->userId);
    }
}
