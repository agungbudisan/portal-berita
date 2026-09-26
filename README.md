# WinniNews

WinniNews adalah portal berita berbasis Laravel 12 untuk membaca, mencari, dan mengelola berita dari beberapa sumber API. Aplikasi menyediakan halaman publik, autentikasi pengguna, bookmark, komentar, serta dashboard administrasi berbasis role.

## Fitur

- Beranda dengan berita pilihan, terbaru, dan populer.
- Daftar berita, detail berita, kategori, dan pencarian.
- Integrasi News API dan GNews melalui sumber API yang dapat dikelola admin.
- Pengambilan berita manual melalui command Artisan dan otomatis setiap jam melalui scheduler Laravel.
- Autentikasi dengan role admin dan user.
- Bookmark berita dan dashboard bookmark pengguna.
- Komentar pengguna, pengeditan/penghapusan komentar sendiri, dan moderasi komentar oleh admin.
- Dashboard admin untuk mengelola berita, kategori, pengguna, komentar, dan sumber API.
- Upload gambar berita melalui Cloudinary.
- Validasi input, proteksi CSRF, hashing password, dan sanitasi HTML menggunakan HTMLPurifier.

## Persyaratan

- PHP 8.2 atau lebih baru.
- Composer.
- Node.js dan npm.
- SQLite (konfigurasi default), MySQL/MariaDB, atau PostgreSQL.
- API key dari News API dan/atau GNews jika ingin mengambil berita eksternal.

## Instalasi Lokal

1. Clone repository dan masuk ke direktori proyek:

	```bash
	git clone https://github.com/agungbudisan/portal-berita.git
	cd portal-berita
	```

2. Pasang dependensi PHP dan JavaScript:

	```bash
	composer install
	npm install
	```

3. Buat file lingkungan dan application key:

	```bash
	cp .env.example .env
	php artisan key:generate
	```

	Pada Windows PowerShell, gunakan `Copy-Item .env.example .env` sebagai pengganti `cp`.

4. Konfigurasikan database di `.env`. Untuk SQLite, buat file database lalu gunakan:

	```env
	DB_CONNECTION=sqlite
	DB_DATABASE=C:/path/to/portal-berita/database/database.sqlite
	```

	Untuk MySQL, isi `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` sesuai server Anda.

5. Isi kredensial sumber berita dan, bila diperlukan, Cloudinary:

	```env
	NEWS_API_KEY=your_news_api_key
	GNEWS_API_KEY=your_gnews_api_key

	CLOUDINARY_URL=cloudinary://api_key:api_secret@cloud_name
	```

6. Jalankan migrasi dan data awal:

	```bash
	php artisan migrate --seed
	php artisan storage:link
	```

7. Jalankan aplikasi dan Vite dalam terminal terpisah:

	```bash
	php artisan serve
	npm run dev
	```

	Buka `http://localhost:8000` di browser. Untuk menjalankan server, queue listener, log viewer, dan Vite sekaligus, gunakan `composer run dev`.

## Akun Seeder

Perintah `php artisan migrate --seed` membuat akun berikut untuk pengembangan lokal:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@winnicode.com` | `password` |
| User | `user@winninews.com` | `password` |

Ganti password tersebut sebelum aplikasi digunakan di lingkungan bersama atau production.

## Fetch Berita

Fetch berita dari semua sumber API yang berstatus aktif dapat dijalankan secara manual:

```bash
php artisan news:fetch
```

Scheduler aplikasi menjalankan command tersebut setiap jam. Pada server Linux, daftarkan scheduler Laravel dengan cron:

```cron
* * * * * cd /path/to/portal-berita && php artisan schedule:run >> /dev/null 2>&1
```

Sumber API, URL, API key, dan status aktif/nonaktif dapat dikelola dari dashboard admin pada `/admin/api-sources`.

## Command List

```bash
php artisan route:list       # Melihat seluruh route aplikasi
php artisan migrate:fresh --seed
php artisan test             # Menjalankan test Pest
npm run build                # Build asset frontend sesuai konfigurasi proyek
```

`migrate:fresh --seed` akan menghapus seluruh tabel dan data. Gunakan hanya pada lingkungan pengembangan atau pengujian.

## Struktur Utama

```text
app/
├── Console/Commands/       # Command Artisan, termasuk news:fetch
├── Http/Controllers/       # Controller publik, user, dan admin
├── Models/                 # Model Eloquent
├── Repositories/           # Akses data berita dan kategori
├── Services/               # Integrasi API berita
└── Providers/              # Scheduler dan service provider
database/
├── migrations/             # Struktur tabel
└── seeders/                # Data awal aplikasi
resources/
├── views/                  # Template Blade
├── css/                    # Style aplikasi
└── js/                     # JavaScript dan Alpine.js
routes/                     # Route web, autentikasi, dan console
public/                     # Entry point dan asset publik
```

## Tech Stack

- Laravel 12 dan PHP 8.2+.
- Blade, Bootstrap 5, Alpine.js, dan Vite.
- SQLite, MySQL/MariaDB, atau PostgreSQL.
- Laravel Sanctum, Laravel Breeze, Pest, dan PHPUnit.
- News API, GNews, Cloudinary, Guzzle, dan HTMLPurifier.

## Pengujian

Jalankan test dengan:

```bash
php artisan test
```

Pastikan konfigurasi database pengujian tersedia sebelum menjalankan test yang membutuhkan database.

## Lisensi

Proyek ini menggunakan lisensi MIT.
