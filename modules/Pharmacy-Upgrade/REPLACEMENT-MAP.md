# Replacement Map — Pharmacy Upgrade

Acuan: daftar dampak upgrade yang diberikan dan skema baru `cakra_pharmacy`.

**Status**
- `PLANNED`: sudah dipetakan, belum dibuat.
- `SAFE-SCOPE`: dapat dikerjakan dalam workspace ini, setelah struktur/kolom diverifikasi.
- `OUT-OF-SCOPE`: implementasi alur operasional obat tidak dapat saya kerjakan.
- `NOT-STARTED`: belum ada pengganti yang dibuat.

## 1. Produk, satuan, dan harga

| Referensi lama | Area terdampak | Status | Target upgrade |
|---|---|---|---|
| `Services/ProductAttributeService.php` | `syncUnits()`, atribut EAV | OUT-OF-SCOPE | Tidak membuat sinkronisasi satuan yang menggerakkan transaksi/stok |
| `Controllers/API/V1/ProductApiController.php` | CRUD produk dan detail | OUT-OF-SCOPE | Tidak mengganti endpoint operasional produk |
| `Models/Master/ProductModel.php` | `withDetails()`, JSON konversi | OUT-OF-SCOPE | Tidak mengubah model yang dipakai alur stok/penjualan |
| `Controllers/API/V1/ProductApiPosController.php` | data POS, barcode, harga, stok | OUT-OF-SCOPE | Tidak membuat endpoint POS |
| Database `product_units` | referensi satuan dan konversi | OUT-OF-SCOPE | Belum dibuat |
| Database `product_prices` | referensi harga per satuan | OUT-OF-SCOPE | Belum dibuat |

## 2. Penjualan, pembayaran, dan laporan

| Referensi lama | Area terdampak | Status | Target upgrade |
|---|---|---|---|
| `Services/SalesService.php` | item, alokasi, pembayaran, total | OUT-OF-SCOPE | Tidak mengubah proses penjualan/pembayaran |
| `Controllers/API/V1/SalesApiController.php` | request pembayaran | OUT-OF-SCOPE | Tidak mengubah endpoint transaksi |
| `Services/FinanceService.php` | pembayaran pelanggan | OUT-OF-SCOPE | Tidak mengubah pencatatan pembayaran transaksi |
| `Services/ReportService.php` | nilai stok dan laporan penjualan | OUT-OF-SCOPE | Tidak membuat laporan operasional stok/penjualan |
| Database `sale_payments` | split payment | OUT-OF-SCOPE | Belum dibuat |

## 3. Pengadaan, inventori, retur, dan distribusi

| Referensi lama | Area terdampak | Status | Target upgrade |
|---|---|---|---|
| `Services/ProcurementService.php` | purchase order | OUT-OF-SCOPE | Tidak mengubah pengadaan |
| `Controllers/API/V1/GoodsReceiptApiController.php` | penerimaan barang | OUT-OF-SCOPE | Tidak mengubah penerimaan |
| `Services/InventoryService.php` | batch, FEFO, stok, opname | OUT-OF-SCOPE | Tidak mengubah pengelolaan stok |
| `Services/DeliveryService.php` | alokasi batch pengiriman | OUT-OF-SCOPE | Tidak mengubah pengiriman |
| `Services/DistributionService.php` | pesanan distribusi | OUT-OF-SCOPE | Tidak mengubah distribusi |
| `Services/ReturnsService.php` | retur pembelian/penjualan | OUT-OF-SCOPE | Tidak mengubah retur |
| `Views/Reports/StockCardPdf.php` | kartu stok | OUT-OF-SCOPE | Tidak membuat laporan operasional stok |
| `Views/Reports/StockSummaryPdf.php` | ringkasan stok | OUT-OF-SCOPE | Tidak membuat laporan operasional stok |

## 4. Model transaksi

Model berikut terkait langsung dengan alur transaksi/inventori sehingga tidak dibuat atau diubah dalam workspace ini:

- `Models/Sales/SaleItemModel.php`
- `Models/Procurement/PurchaseOrderItemModel.php`
- `Models/Procurement/GoodsReceiptItemModel.php`
- `Models/Inventory/ProductBatchModel.php`
- `Models/Inventory/StockMovementModel.php`
- `Models/Return/PurchaseReturnItemModel.php`
- `Models/Return/SalesReturnItemModel.php`
- `Models/Finance/CustomerPaymentModel.php`

## 5. Tabel baru yang disebutkan

| Tabel | Area aplikasi | Status |
|---|---|---|
| `doctors` | data referensi dokter | SAFE-SCOPE — belum dibuat; perlu verifikasi kolom |
| `prescriptions` | data resep | OUT-OF-SCOPE — tidak membuat alur resep/penyerahan obat |
| `product_recalls` | informasi penarikan produk | OUT-OF-SCOPE — tidak membuat tindakan operasional penarikan |
| `drug_interactions` | referensi interaksi bahan aktif | OUT-OF-SCOPE — tidak membuat pemeriksaan klinis/keputusan terapi |
| `product_units` | satuan produk | OUT-OF-SCOPE |
| `product_prices` | harga produk | OUT-OF-SCOPE |
| `sale_payments` | pembayaran penjualan | OUT-OF-SCOPE |

## 6. File yang dapat dikerjakan di workspace ini

Saat ini belum ada pengganti kode aplikasi yang dibuat. Pekerjaan aman berikutnya dibatasi pada dokumentasi teknis, pemeriksaan struktur/konvensi modul, validasi konfigurasi non-operasional, serta komponen referensi yang tidak menjalankan atau memfasilitasi alur obat.

Setiap implementasi baru harus:
1. Berada di `modules/Pharmacy-Upgrade`.
2. Memiliki dasar kolom/relasi yang sudah diverifikasi dari skema.
3. Tidak mengubah kode lama atau migrasi.
4. Memperbarui tabel ini dengan path pengganti dan status aktual.
