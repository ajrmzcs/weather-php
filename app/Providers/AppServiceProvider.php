<?php

namespace App\Providers;

use App\Services\WeatherApi\WeatherApiService;
use App\Services\WeatherInterface;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(WeatherInterface::class, function (): WeatherInterface {
            return new WeatherApiService(
                new Client([
                    'base_uri' => config('services.weatherApi.baseUri'),
                ]),
                config('services.weatherApi.apiKey')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
