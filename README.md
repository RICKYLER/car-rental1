# ECROS

Electric Car Rental Operations System built with Laravel and SQLite mock data.

## Included modules

- Customer overview page with fleet and charging highlights
- Fleet browser with status, connector, zone, and battery filters
- Vehicle detail page with pricing, range, and nearby charging stations
- Booking form that validates safe EV range before confirming a reservation
- Admin dashboard for fleet health, charging queue, alerts, and revenue snapshots

## Local run

From `C:\Users\User\Desktop\dane\ecros`:

```powershell
php artisan migrate:fresh --seed
php artisan serve
```

Then open [http://127.0.0.1:8000](http://127.0.0.1:8000).

If you prefer the helper script:

```powershell
powershell -ExecutionPolicy Bypass -File .\start-ecros.ps1
```

## Useful commands

```powershell
php artisan migrate:fresh --seed
php artisan test
```

Requirements:

- PHP 8.3+
- Composer
- Node.js / npm
