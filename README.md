# WinniNews - Portal Berita dengan Laravel

Aplikasi portal berita yang terintegrasi dengan API Berita, dibangun dengan Laravel 12+ dan Bootstrap 5.

## Fitur

- Integrasi dengan API Berita eksternal
- Portal berita responsif
- Dashboard Admin untuk manajemen konten
- Dashboard User untuk bookmark dan komentar
- Sistem authentikasi multi-role (admin, user)

## Instalasi

1. Clone repository
   ```
   git clone https://your-repository-url/winninews.git
   cd winninews
   ```

2. Install dependensi
   ```
   composer install
   npm install
   ```

3. Setup lingkungan
   ```
   cp .env.example .env
   php artisan key:generate
   ```

4. Konfigurasi database di file .env

5. Jalankan migrasi dan seeder
   ```
   php artisan migrate --seed
   ```

6. Link storage untuk gambar
   ```
   php artisan storage:link
   ```

7. Kompilasi assets
   ```
   npm run dev
   ```

8. Jalankan aplikasi
   ```
   php artisan serve
   ```

## Penggunaan

### Akun Default

**Admin:**
- Email: admin@winnicode.com
- Password: password

**User:**
- Email: ahmad@example.com
- Password: password

### Fetch Berita dari API

Jalankan perintah berikut untuk mengambil berita dari API:

```
php artisan news:fetch
```

Untuk menjadwalkan fetch otomatis setiap jam, pastikan cron job Laravel terpasang dengan benar:

```
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## Struktur Project

- `app/Models` - Model database
- `app/Http/Controllers` - Controller
- `app/Repositories` - Repository pattern untuk akses data
- `app/Services` - Service untuk logika bisnis
- `database/migrations` - Migrasi database
- `database/seeders` - Seeder untuk data awal
- `resources/views` - Template view

<!-- ## Author

PT. WINNICODE GARUDA TEKNOLOGI
Alamat (Pusat): Bandung - Jl. Asia Afrika No.158, Kb. Pisang, Kec. Sumur Bandung, Kota Bandung, Jawa Barat 40261
Alamat (Cabang): Bantul, Yogyakarta
Call Center: 6285159932501 (24 Jam)

## License

The MIT License (MIT) -->
