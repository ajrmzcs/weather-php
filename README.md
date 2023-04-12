# Weather php 
Backend to support [Wearther Vuetify](https://github.com/ajrmzcs/weather-vuetify) demo project, in Laravel 10, using a sqlite database.

### System Requirement:
1. Local PHP 8.1 and Composer

### Installation Steps:
1. Clone this repository
2. Create `.env` file: `cp .env.example .env`
3. Install dependencies: `composer install`
4. Generate key: `php artisan key:generate`
5. Create database.sqlite file in `weather-php/database` folder
6. Run migrations and seeders: `php artisan migrate:fresh --seed`
7. Run local server: `php artisan serve`
