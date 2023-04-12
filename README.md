# Weather php 
Backend to support [Wearther Vuetify](https://github.com/ajrmzcs/weather-vuetify) demo project, in Laravel 10, using a sqlite database.

### System Requirement:
1. Local PHP 8.1 and Composer

### Installation Steps:
1. Clone this repository
2. Create `.env` file: `cp .env.example .env`
3. Sign up and obtain an API key on [Weather API](https://www.weatherapi.com/)
4. Add `http://api.weatherapi.com` to `WEATHER_BASE_URI API` and your api key to WEATHER_BASE_URI in `.env`
5. Install dependencies: `composer install`
6. Generate key: `php artisan key:generate`
7. Create database.sqlite file in `weather-php/database` folder
8. Run migrations and seeders: `php artisan migrate:fresh --seed`
9. Run local server: `php artisan serve`
