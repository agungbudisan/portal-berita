# WinniNews - Portal Berita dengan Laravel

Aplikasi portal berita modern yang terintegrasi dengan API Berita, dibangun dengan Laravel 12+ dan Bootstrap 5. Platform ini menyediakan pengalaman membaca berita yang responsif dengan fitur lengkap untuk admin dan user.

## 🚀 Fitur Utama

- ✨ Integrasi dengan API Berita eksternal untuk konten yang selalu terbaru
- 📱 Portal berita responsif yang optimal di semua perangkat
- 🔐 Dashboard Admin untuk manajemen konten berita
- 👤 Dashboard User untuk bookmark, komentar, dan preferensi
- 🔑 Sistem autentikasi multi-role (Admin, User)
- 📅 Penjadwalan otomatis untuk fetch berita (Cron Jobs)
- 💾 Repository pattern untuk clean code
- 🎨 Desain modern dengan Bootstrap 5

## 📋 Persyaratan Sistem

- PHP 8.3 atau lebih tinggi
- Composer
- Node.js & npm
- MySQL/MariaDB atau PostgreSQL
- Laravel 12+

## 🛠️ Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/agungbudisan/portal-berita.git
cd portal-berita
```

### 2. Install Dependensi

```bash
composer install
npm install
```

### 3. Setup Lingkungan

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=portal_berita
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan Migrasi dan Seeder

```bash
php artisan migrate --seed
```

### 6. Link Storage

```bash
php artisan storage:link
```

### 7. Kompilasi Assets

```bash
npm run dev
```

Untuk production:

```bash
npm run build
```

### 8. Jalankan Aplikasi

```bash
php artisan serve
```

Aplikasi akan dapat diakses di `http://localhost:8000`

## 👥 Akun Default

### Admin
- **Email:** admin@winnicode.com
- **Password:** password

### User
- **Email:** ahmad@example.com
- **Password:** password

> ⚠️ **Penting:** Ubah password default segera setelah instalasi untuk keperluan keamanan!

## 📰 Fetch Berita dari API

### Manual Fetch

Jalankan perintah berikut untuk mengambil berita dari API:

```bash
php artisan news:fetch
```

### Automatic Scheduling

Untuk menjadwalkan fetch otomatis setiap jam, pastikan cron job Laravel terpasang dengan benar di server Anda:

```bash
* * * * * cd /path/to/portal-berita && php artisan schedule:run >> /dev/null 2>&1
```

## 📁 Struktur Project

```
portal-berita/
├── app/
│   ├── Models/              # Model database
│   ├── Http/
│   │   ├── Controllers/     # Controller aplikasi
│   │   └── Requests/        # Form requests & validation
│   ├── Repositories/        # Repository pattern untuk akses data
│   └── Services/            # Service untuk logika bisnis
├── database/
│   ├── migrations/          # Migrasi database
│   └── seeders/             # Seeder untuk data awal
├── resources/
│   ├── views/               # Template Blade
│   ├── css/                 # CSS custom
│   └── js/                  # JavaScript
├── routes/                  # Route definisi
├── config/                  # Konfigurasi aplikasi
└── public/                  # File public (images, css, js)
```

## 🔌 API Integration

Aplikasi ini menggunakan API eksternal untuk fetch berita. Pastikan untuk mengkonfigurasi API key di file `.env`:

```env
NEWS_API_KEY=your_api_key_here
NEWS_API_URL=https://newsapi.org/v2
```

## 💻 Tech Stack

| Teknologi | Versi | Deskripsi |
|-----------|-------|-----------|
| Laravel | 12+ | Framework PHP |
| Blade | 75.8% | Template engine |
| PHP | 24% | Server-side language |
| Bootstrap | 5 | CSS Framework |
| MySQL | - | Database |
| Node.js | - | JavaScript runtime |

## 🔒 Keamanan

- Input validation di semua form
- CSRF protection dengan Laravel
- Password hashing dengan bcrypt
- Authorization checks di controllers
- Rate limiting untuk API endpoints

## 📝 License

MIT License - lihat file LICENSE untuk detail lebih lanjut.

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan fork repository ini dan buat pull request untuk perubahan yang ingin Anda usulkan.

## 📧 Kontak & Support

Untuk pertanyaan atau support:
- GitHub Issues: [Buka issue](https://github.com/agungbudisan/portal-berita/issues)
- Email: agungbudisan@example.com

## 📅 Changelog

Lihat [CHANGELOG.md](CHANGELOG.md) untuk riwayat perubahan dan update.

---

**Terakhir diperbarui:** September 2, 2026

Dibuat dengan ❤️ oleh [Agung Budisan](https://github.com/agungbudisan)
