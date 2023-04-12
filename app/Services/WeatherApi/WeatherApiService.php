<?php

namespace App\Services\WeatherApi;

use App\Models\City;
use App\Services\WeatherInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class WeatherApiService implements WeatherInterface
{
    private const FORECAST_DAYS = 5;

    public function __construct(
        private readonly Client $client,
        private readonly string $apiKey,
    ) {
    }

    public function showMany(Collection $cities): array
    {
        $weathers = $cities->map(function ($city): array {
            $response = $this->client->request('GET', '/v1/current.json', [
                'query' => [
                    'key' => $this->apiKey,
                    'q' => Str::slug($city->name, '_') . "  , $city->state"
                ]

            ]);
            $cityWeather = json_decode($response->getBody(), true);
            $cityWeather['city_id'] = $city->id;
            return $cityWeather;
        });

        return self::buildManyResponse($weathers);
    }

    public function show(City $city): array
    {
        $response = $this->client->request('GET', '/v1/forecast.json', [
            'query' => [
                'key' => $this->apiKey,
                'q' => Str::slug($city->name, '_') . "  , $city->state",
                'days' => self::FORECAST_DAYS,
                'aqi' => 'no',
                'alerts' => 'no',
            ]
        ]);

        return self::buildShowResponse(json_decode($response->getBody(), true));
    }

    private static function buildManyResponse(Collection $weathers): array
    {
        return [
            'data' => $weathers->map(function ($weather) {
                return [
                    'city_id' => $weather['city_id'],
                    'city' => $weather['location']['name'] ?? '',
                    'state' => $weather['location']['region'] ?? '',
                    'temp_c' => $weather['current']['temp_c'] ?? '',
                    'temp_f' => $weather['current']['temp_f'] ?? '',
                    'condition_text' => $weather['current']['condition']['text'] ?? '',
                    'condition_icon' => $weather['current']['condition']['icon'] ?? '',
                    'feelslike_c' => $weather['current']['feelslike_c'] ?? '',
                    'feelslike_f' => $weather['current']['feelslike_f'] ?? '',
                    'uv' => $weather['current']['uv'] ?? '',
                ];
            }),
        ];
    }

    private static function buildShowResponse(array $weather): array
    {
        $response = [
            'data' => [
                'city' => $weather['location']['name'] ?? '',
                'state' => $weather['location']['region'] ?? '',
                'temp_c' => $weather['current']['temp_c'] ?? '',
                'temp_f' => $weather['current']['temp_f'] ?? '',
                'condition_text' => $weather['current']['condition']['text'] ?? '',
                'condition_icon' => $weather['current']['condition']['icon'] ?? '',
            ],
        ];

        $forecast = [];
        foreach ($weather['forecast']['forecastday'] as $forecastDay) {
            $forecast[] = [
                'date' => $forecastDay['date'],
                'maxtemp_c' => $forecastDay['day']['maxtemp_c'] ?? '',
                'maxtemp_f' => $forecastDay['day']['maxtemp_f'] ?? '',
                'mintemp_c' => $forecastDay['day']['mintemp_c'] ?? '',
                'mintemp_f' => $forecastDay['day']['mintemp_f'] ?? '',
                'avgtemp_c' => $forecastDay['day']['avgtemp_c'] ?? '',
                'avgtemp_f' => $forecastDay['day']['avgtemp_f'] ?? '',
                'condition_text' => $forecastDay['day']['condition']['text'] ?? '',
                'condition_icon' => $forecastDay['day']['condition']['icon'] ?? '',
                'sunrise' => $forecastDay['astro']['sunrise'] ?? '',
                'sunset' => $forecastDay['astro']['sunset'] ?? '',
            ];
        }

        $response['data']['forecast'] = $forecast;
        return $response;
    }
}
