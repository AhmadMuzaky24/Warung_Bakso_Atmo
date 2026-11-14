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
    cd bakso_atmo
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

    Pada Windows, file `.env.example` juga dapat disalin secara manual menjadi `.env`.

6. Buat application key:

    ```bash
    php artisan key:generate
    ```

7. Buat database MySQL dengan nama yang sesuai konfigurasi `.env`, kemudian sesuaikan konfigurasi koneksi database pada file tersebut.

8. Jalankan migrasi dan seeder:

    ```bash
    php artisan migrate --seed
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

Aplikasi dapat diakses melalui:

```text
http://localhost:8000
```

## Fitur Sprint 1

Sprint 1 berfokus pada fondasi aplikasi, autentikasi, dan manajemen pengguna.

- Login menggunakan email dan password.
- Logout dengan invalidasi session.
- Pembatasan percobaan login.
- Pemeriksaan status akun aktif.
- Middleware autentikasi dan otorisasi berdasarkan role.
- Pengubahan password.
- Manajemen pengguna oleh admin.
- Pencegahan penonaktifan akun admin sendiri.
- Pencegahan penghapusan akun sendiri.

### Role Pengguna

| Role       | Keterangan                                           |
| ---------- | ---------------------------------------------------- |
| `admin`    | Memiliki akses ke manajemen pengguna dan area admin. |
| `staff`    | Pengguna operasional.                                |
| `customer` | Pengguna pelanggan.                                  |

## Akun Pengujian

Seeder menyediakan akun berikut untuk keperluan pengujian lokal:

| Email                           | Password            | Role     | Status      |
| ------------------------------- | ------------------- | -------- | ----------- |
| `admin.test@baksoatmo.local`    | `BaksoAtmoTest123!` | admin    | Aktif       |
| `inactive.test@baksoatmo.local` | `BaksoAtmoTest123!` | customer | Tidak aktif |

Jalankan ulang seeder menggunakan:

```bash
php artisan db:seed
```

**Catatan:** Akun tersebut hanya untuk pengembangan dan pengujian lokal. Jangan gunakan kredensial pengujian sebagai akun produksi.

## Automated Testing

Jalankan seluruh pengujian menggunakan:

```bash
php artisan test
```

Pengujian Sprint 1 mencakup:

- Autentikasi.
- Otorisasi berdasarkan role.
- Pengubahan password.
- Manajemen pengguna admin.

## Struktur Pengembangan

Pengembangan proyek menggunakan pendekatan sprint dan feature branch.

Setiap kontributor mengerjakan fitur pada branch masing-masing. Integrasi antarbranch dan sprint dilakukan oleh Project Leader setelah proses review dan pengujian.

## Status Proyek

Proyek sedang dalam tahap pengembangan. Fitur akan ditambahkan secara bertahap sesuai pembagian sprint.

---

**Bakso Atmo — Project Development**
