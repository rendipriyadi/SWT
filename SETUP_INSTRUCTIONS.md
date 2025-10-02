# 🚀 Setup Instructions untuk Developer Baru

## 📋 Prerequisites
- PHP 8.2
- Composer
- MySQL/MariaDB
- Node.js & NPM (untuk asset compilation)

## 🔧 Langkah Setup

### 1. Clone & Install Dependencies
```bash
git clone [repository-url]
cd SWT
composer install
npm install
```

### 2. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Edit .env file sesuai database lokal:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=swt_db
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Database Setup
```bash
# Buat database baru
mysql -u root -p
CREATE DATABASE swt_db;
exit;

# Jalankan migrations
php artisan migrate

# Jalankan seeders (data master saja)
php artisan db:seed

# Optional: Jika ingin sample laporan untuk testing
# php artisan db:seed --class=LaporanSeeder
```

### 4. Storage Setup
```bash
# Link storage untuk file uploads
php artisan storage:link

# Set permissions (Linux/Mac)
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### 5. Asset Compilation (Optional)
```bash
# Compile assets
npm run dev
# atau untuk production
npm run build
```

### 6. Start Development Server
```bash
php artisan serve
```

## 📊 Data yang Akan Tersedia Setelah Setup

### Master Data (dari Seeders):
- ✅ **3 Areas**: Manufacture, Quality Control, Warehouse
- ✅ **15 Penanggung Jawab**: Semua PIC dengan station yang benar
- ✅ **2 Problem Categories**: 
  - Safety: Potential hazard
  - 5S: Seiri, Seiton, Seiketsu, Shitsuke
- ✅ **1 Test User**: email: test@example.com, password: password
- ✅ **Clean Database**: Tidak ada sample laporan - mulai dengan data bersih

### Yang TIDAK Ada:
- ❌ Data laporan pribadi developer sebelumnya
- ❌ File foto yang sudah diupload
- ❌ User accounts yang sudah dibuat manual

## 🔍 Verifikasi Setup Berhasil
1. Buka browser: `http://localhost:8000`
2. Login dengan: `test@example.com` / `password`
3. Check halaman Reports - harus kosong (database bersih)
4. Check Master Data - harus ada semua area dan penanggung jawab
5. Test create laporan baru - dropdown harus terisi lengkap
6. Test create laporan pertama - semua fitur harus berfungsi

## 🚨 Troubleshooting
- **Migration error**: Pastikan database kosong atau drop semua table
- **Permission error**: Set chmod 775 untuk storage dan bootstrap/cache
- **Seeder error**: Jalankan `php artisan migrate:fresh --seed`
- **Asset error**: Jalankan `npm install` dan `npm run dev`

## 📝 Notes
- File `.env` tidak di-commit, jadi harus setup manual
- Database lokal tidak di-sync, jadi data pribadi aman
- Storage files tidak di-commit, jadi foto uploads tidak tersync
