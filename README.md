# Safety Walk and Talk

Safety and 5S issue reporting system for Siemens production environment.

## Key Features

- Issue reporting with photos 
- Automatic assignment to PIC (Person In Charge)
- Completion status tracking
- Report filtering and search
- Complete report history
- Automatic email notifications
- Email reminders (H-2 deadline and overdue)
- Master data management (Area, Department, Problem Category)
- Export completed reports to PDF
- Dashboard with statistical charts
- Responsive design

## Tech Stack

- Laravel 11
- PHP 8.2
- MySQL 8
- Bootstrap 5
- DataTables (Yajra)
- SweetAlert2
- DomPDF
- Chart.js

## Installation

### For Fresh Install
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

4. Configure database in `.env`
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

### For Updates from Git Pull
1. Pull latest code
```bash
git pull origin main
```

2. Install dependencies
```bash
composer install
npm install
```

3. Run migration (if any)
```bash
php artisan migrate:fresh
php artisan migrate
```

4. Update data with seeder
```bash
php artisan db:seed
```

5. Clear cache
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

6. Build assets (if there are changes)
```bash
npm run build
```

7. Start development server
```bash
php artisan serve
```

## Email Configuration

Setup email in `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Safety Walk and Talk"
```

**Note**: Use App Password if using Gmail.

## Scheduled Tasks

Setup cron job on server for email reminders:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Available commands:
- `php artisan report:reminder-deadline` - Reminder H-2 before deadline
- `php artisan report:reminder-overdue` - Reminder for overdue reports

## Important Notes

- Database will be automatically setup with master data (departments, areas, problem categories)
- No dummy reports will be generated
- Seeder uses `updateOrCreate()` so it's safe to run multiple times
- Make sure MySQL/MariaDB is running before running migrations
- Ensure `public/images/reports` and `public/images/completions` folders have write permissions
- For production: set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
