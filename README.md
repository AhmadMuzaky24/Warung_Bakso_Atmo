# Bakso Atmo

Aplikasi web untuk mendukung pengelolaan operasional dan layanan Bakso Atmo.

## Teknologi

- **Framework:** Laravel 13
- **PHP:** 8.5+
- **Database:** MySQL
- **Frontend:** Blade, HTML, CSS, JavaScript
- **Dependency Manager:** Composer dan npm

## Persyaratan Sistem

Pastikan perangkat telah memiliki:

- PHP 8.5 atau versi yang kompatibel dengan proyek.
- Composer.
- Node.js dan npm.
- MySQL.
- Git.

## Instalasi

1. Clone repository:

    ```bash
    git clone <repository-url>
    ```

2. Masuk ke direktori proyek:

    ```bash
    cd Warung_Bakso_Atmo
    ```

3. Instal dependency PHP:

    ```bash
    composer install
    ```

4. Instal dependency frontend:

    ```bash
    npm install
    ```

5. Salin file konfigurasi environment:

    ```bash
    cp .env.example .env
    ```

    Pada Windows PowerShell, dapat menggunakan:

    ```powershell
    Copy-Item .env.example .env
    ```

6. Buat application key:

    ```bash
    php artisan key:generate
    ```

7. Buat database MySQL dengan nama yang sesuai konfigurasi `.env` (default: `bakso_atmo`), kemudian sesuaikan konfigurasi koneksi database pada file tersebut.

8. Jalankan migrasi, seeder, dan symbolic link storage:

    ```bash
    php artisan migrate --seed
    php artisan storage:link
    ```

    Atau untuk membangun ulang dari database kosong:

    ```bash
    php artisan migrate:fresh --seed
    ```

## Menjalankan Aplikasi

Jalankan server Laravel:

```bash
php artisan serve
```

Jalankan frontend development server pada terminal terpisah:

```bash
npm run dev
```

*(Untuk mode produksi/build statis tanpa Vite dev server, jalankan `npm run build` lalu `php artisan serve`.)*

Aplikasi dapat diakses melalui:

```text
http://localhost:8000
```

## Ringkasan Fitur

- **Halaman Publik (Guest):** Beranda / company profile, katalog & detail menu, informasi lokasi/cabang, serta tautan ke GoFood dan ShopeeFood.
- **Autentikasi & Manajemen User (Sprint 1):** Login (email/nomor HP), registrasi Customer, logout, pembatasan percobaan login, pemeriksaan status aktif, middleware role (`admin`, `staff`, `customer`), ubah password, dan CRUD user oleh Admin.
- **Kategori & Menu (Sprint 2):** CRUD kategori dan menu oleh Admin lengkap dengan upload foto via Laravel Storage.
- **Customer Katering (Sprint 4):** Pemesanan katering dengan perhitungan harga/DP di server, snapshot item menu, transaksi atomik, riwayat/detail pesanan milik sendiri, dan konfirmasi WhatsApp.
- **Operasional Staff & Verifikasi Pembayaran Admin (Sprint 5):** Pemisahan kewenangan status (`Staff` mengubah `status` pesanan; `Admin` memverifikasi `payment_status`), pencarian, filter rentang tanggal, dan pagination.
- **Dashboard Admin, Laporan & Ekspor XLSX (Sprint 6):** Ringkasan pesanan, keuangan DP, acara mendatang, data master, laporan rentang tanggal, dan ekspor file `.xlsx` (OpenSpout).

### Role Pengguna

| Role       | Keterangan                                                                  |
| ---------- | --------------------------------------------------------------------------- |
| `admin`    | Dashboard admin, kelola user/kategori/menu, verifikasi `payment_status`, laporan & ekspor XLSX. |
| `staff`    | Dashboard operasional dan pengelolaan `status` pesanan katering.            |
| `customer` | Pemesanan katering serta melihat riwayat dan detail pesanan miliknya sendiri. |

## Akun Pengujian & Data Demo

Seeder menyediakan akun uji berikut untuk keperluan pengembangan dan pengujian lokal (menggunakan data sintetis, bukan data pribadi nyata):

| Email                           | Password            | Role     | Status      |
| ------------------------------- | ------------------- | -------- | ----------- |
| `admin.test@baksoatmo.local`    | `BaksoAtmoTest123!` | admin    | Aktif       |
| `staff.test@baksoatmo.local`    | `BaksoAtmoTest123!` | staff    | Aktif       |
| `customer.test@baksoatmo.local` | `BaksoAtmoTest123!` | customer | Aktif       |
| `inactive.test@baksoatmo.local` | `BaksoAtmoTest123!` | customer | Tidak aktif |

Selain akun di atas, `DatabaseSeeder` juga membuat akun customer demo tambahan, 4 kategori, 6 menu contoh beserta gambar, dan **32 pesanan katering demo** lengkap dengan item menu untuk keperluan uji filter, pagination, dashboard, dan laporan XLSX.

Jalankan ulang seeder menggunakan:

```bash
php artisan db:seed
```

**Catatan:** Akun dan data tersebut hanya untuk pengembangan dan pengujian lokal. Jangan gunakan kredensial pengujian sebagai akun produksi.

## Automated Testing

Jalankan seluruh pengujian menggunakan:

```bash
php artisan test
```

Cakupan pengujian mencakup:

- Autentikasi, registrasi, role middleware, ubah password, dan manajemen pengguna Admin.
- CRUD kategori/menu serta upload foto storage.
- Akses publik Guest dan katalog menu.
- Pemesanan katering Customer, perhitungan server, dan kepemilikan data (ownership).
- Pemisahan kewenangan Staff (`status`) & Admin (`payment_status`), pencarian, filter, dan pagination.
- Dashboard Admin, filter laporan rentang tanggal, ekspor XLSX, dan validasi seeder.

## Struktur Pengembangan

Pengembangan proyek menggunakan pendekatan sprint dan feature branch.

Setiap kontributor mengerjakan fitur pada branch masing-masing. Integrasi antarbranch dan sprint dilakukan oleh Project Leader setelah proses review dan pengujian.

## Status Proyek

Proyek sedang dalam tahap pengembangan. Fitur akan ditambahkan secara bertahap sesuai pembagian sprint.

---

**Bakso Atmo — Project Development**
