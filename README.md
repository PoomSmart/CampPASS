# CampPASS
Web-based CampHUB-inspired Thai Student Camp Application and Management System.

## Prerequisites
- PHP 7+
- Laravel 5.8+
- MySQL Server
- Node.js

## Setup
- Get yourself the `.env` file
- Use the provided `php.ini` file
- `cd` to the project root directory
- `composer install`
- `npm install`

## Compiling Assets
- `npm run dev` or `npm run production` for compiling app's SASS & JS (Do this every time you make changes to them)

## Data Seeding
- `php artisan migrate:fresh --seed`

## Testing
- `./localrun`
