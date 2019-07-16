# CampPASS
Web-based CampHUB-inspired Thai Student Camp Application and Management System.

# Unfortunate History
Although this project was built to aim for solving the tedious process in registering for a camp from both Campers and Camp Makers perspectives, the organization partnered with the CampPASS team semi-childishly refused to take over the project and improve to succession and release. The world is unfair, sometimes. People do not think about each other enough. People are harsh, especially with those who have the power in this context. This is Thailand in a nutshell.

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
