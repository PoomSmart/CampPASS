# CampPASS
Web-based CampHUB-inspired Thai Student Camp Application and Management System.

## Notices
This project has been built as a proof-of-concept of an information system for Thai camps. It is **NOT** affiliated with CampHUB, nor can they make use of, copy, redistribute and modify the project.

## Prerequisites
- PHP 7+
- Laravel 5.8+
- MySQL Server
- Node.js (`npm`)

## Setup
- Get yourself the `.env` file
- Use the provided `php.ini` file
- `cd` to the project root directory
- `composer install`
- `npm install`
- `php artisan storage:link`

## Compiling Assets
- `npm run dev` or `npm run production` for compiling app's SASS & JS (Do this every time you make changes to them)

## Data Seeding
- `php artisan migrate:fresh --seed`

## Testing
- `./localrun`
