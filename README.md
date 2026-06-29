# Nexus ERP

Nexus ERP is a Laravel-based administrative system for managing users, customers, product inventory, and invoices.

## Requirements

- PHP 8.4.1 or newer
- Composer
- Node.js and npm
- MySQL or MariaDB
- A local web stack such as Herd, XAMPP, Laragon, or Valet

> Important: this project may fail Artisan and Composer commands on PHP 8.2 because the installed dependencies require PHP 8.4.1 or newer.

## Fresh Setup

Clone the project and enter the project directory:

```bash
git clone <repository-url>
cd ERP
```

Install PHP dependencies:

```bash
composer install
```

Install frontend dependencies:

```bash
npm install
```

Create the environment file:

```bash
cp .env.example .env
```

On Windows PowerShell, use:

```powershell
Copy-Item .env.example .env
```

Generate the Laravel application key:

```bash
php artisan key:generate
```

## Database Setup

Create a MySQL database named `erp`.

Using MySQL CLI:

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Or create it manually in phpMyAdmin:

1. Open phpMyAdmin.
2. Click `New`.
3. Enter database name `erp`.
4. Choose `utf8mb4_unicode_ci` collation.
5. Click `Create`.

Update these values in `.env` if your local database credentials are different:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=erp
DB_USERNAME=root
DB_PASSWORD=
```

## Run Migrations and Seeders

Run all database migrations:

```bash
php artisan migrate
```

Seed the sample ERP data:

```bash
php artisan db:seed
```

For a fresh reset with all seed data:

```bash
php artisan migrate:fresh --seed
```

The seeder creates sample customers, products, invoices, and invoice items.

## Create the First Admin User

The default seeder does not create a login user. Create one with Tinker:

```bash
php artisan tinker --execute 'App\Models\User::updateOrCreate(["email" => "admin@example.com"], ["name" => "Admin User", "password" => Illuminate\Support\Facades\Hash::make("password")]);'
```

Login credentials after running that command:

```text
Email: admin@example.com
Password: password
```

Change this password from the Users page after logging in.

## Run the Application

Start the Laravel development server:

```bash
php artisan serve
```

Open the app:

```text
http://127.0.0.1:8000
```

For frontend development with Vite:

```bash
npm run dev
```

For a production-style frontend build:

```bash
npm run build
```

You can also run the combined Laravel development command from `composer.json`:

```bash
composer run dev
```

That starts the Laravel server, queue listener, and Vite dev server together.

## Useful Commands

Clear cached Laravel state:

```bash
php artisan optimize:clear
```

Show registered routes:

```bash
php artisan route:list
```

Run tests:

```bash
php artisan test --compact
```

Format PHP code:

```bash
vendor/bin/pint --dirty --format agent
```

## Troubleshooting

If Artisan fails with a Composer platform error, check your PHP version:

```bash
php -v
```

Use PHP 8.4.1 or newer, then rerun the command.

If the app cannot connect to the database:

1. Make sure MySQL is running.
2. Confirm the `erp` database exists.
3. Check `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env`.
4. Run `php artisan config:clear`.

If login fails on a fresh database, create the first admin user with the Tinker command above.

If seeded customers or products cannot be deleted, they may already be used by invoices. Delete related invoices first, or keep the records for invoice history.
