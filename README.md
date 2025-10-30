# Safety Walk and Talk

Sistem pelaporan masalah safety dan 5S untuk lingkungan produksi Siemens.

## Fitur Utama

- Pelaporan masalah dengan foto
- Penugasan otomatis ke supervisor
- Tracking status penyelesaian
- Filter dan pencarian laporan
- Riwayat laporan lengkap
- Notifikasi email
- Responsive design

## Tech Stack

- Laravel 10
- MySQL 8
- Bootstrap 5
- DataTables
- SweetAlert2

## Instalasi

### Untuk Setup Baru (Fresh Install)
1. Clone repository
```bash
git clone https://github.com/rendipriyadi/SWT.git
cd SWT
```

2. Install dependencies
```bash 
composer install
npm install
```

3. Setup environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Konfigurasi database di `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=swt_db
DB_USERNAME=root
DB_PASSWORD=
```

5. Setup database (fresh install)
```bash
php artisan migrate:fresh --seed
```

6. Build assets
```bash
npm run build
```

7. Clear cache
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

8. Start development server
```bash
php artisan serve
```

### Untuk Update dari Git Pull
1. Pull kode terbaru
```bash
git pull origin main
```

2. Install dependencies
```bash
composer install
npm install
```

3. Jalankan migration (jika ada)
```bash
php artisan migrate:fresh
php artisan migrate
```

4. Update data dengan seeder
```bash
php artisan db:seed
```

5. Clear cache
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

6. Build assets (jika ada perubahan)
```bash
npm run build
```

7. Start development server
```bash
php artisan serve
```

## Catatan Penting
- Database akan otomatis ter-setup dengan data master (departemen, area, problem categories)
- Tidak ada dummy laporan yang di-generate
- Seeder menggunakan `updateOrCreate()` sehingga aman dijalankan berulang kali
- Pastikan MySQL/MariaDB sudah running sebelum menjalankan migration
