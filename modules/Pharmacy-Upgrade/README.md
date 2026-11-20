# Pharmacy Upgrade

Workspace terisolasi untuk menyiapkan upgrade kode berdasarkan skema database terbaru `cakra_pharmacy`.

## Prinsip kerja

- Kode lama di `modules/Pharmacy` hanya menjadi referensi; tidak diubah.
- Semua file upgrade berada di `modules/Pharmacy-Upgrade`.
- Skema database terbaru menjadi acuan; tidak membuat atau menjalankan migrasi.
- Tidak mengubah Dynamic Routes, autentikasi Shield, atau fondasi CI4.
- Pola kode lama dipertahankan sejauh masih sesuai dengan skema baru.
- Perubahan harus dicatat pada `REPLACEMENT-MAP.md`.

## Struktur

- `Models/` — model non-operasional yang aman dan diperlukan oleh komponen referensi.
- `Services/` — layanan umum/non-operasional.
- `Controllers/` — endpoint non-operasional yang aman.
- `Validation/` — aturan validasi umum.
- `Config/` — konfigurasi modul bila memang diperlukan.
- `Views/` — tampilan non-operasional.
- `REPLACEMENT-MAP.md` — peta file lama, status, dan target penggantinya.

## Batas implementasi

Workspace ini tidak digunakan untuk membuat atau mengubah alur operasional pengadaan, penerimaan, pengelolaan stok, penjualan, pembayaran transaksi, retur, penyerahan, maupun distribusi obat. Bagian tersebut dicatat pada replacement map agar statusnya transparan, bukan dianggap sudah dikerjakan.

## Aturan perubahan

1. Jangan mengubah file di luar `modules/Pharmacy-Upgrade`.
2. Jangan menyalin implementasi lama secara otomatis; periksa kesesuaian skema terlebih dahulu.
3. Jangan mengasumsikan kolom, relasi, atau konfigurasi yang belum diverifikasi.
4. Pertahankan integrasi autentikasi aplikasi utama.
5. Catat setiap file yang dibuat atau diganti beserta statusnya di replacement map.
