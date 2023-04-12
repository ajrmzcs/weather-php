<?php

namespace Tests\Feature;

use App\Models\City;
use App\Services\WeatherApi\WeatherApiService;
use App\Services\WeatherInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class CityControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_lists_cities_with_weather(): void
    {
        config()->set('services.weatherApi.baseUri', 'https://api.test.com');
        config()->set('services.weatherApi.apiKey', 'secret');

        $this->instance(
            WeatherInterface::class,
            Mockery::mock(WeatherApiService::class, function (MockInterface $mock) {
                $mock->shouldReceive('showMany')->once()
                ->andReturn([
                    'data' => [
                        [
                            'city_id' => 1,
                            'city' => 'Orlando',
                            'state' => 'Florida',
                            'temp_c' => 25,
                            'temp_f' => 77,
                            'condition_text' => 'Clear',
                            'condition_icon' => '//cdn.weatherapi.com/weather/64x64/night/113.png',
                            'feelslike_c' => 26.6,
                            'feelslike_f' => 79.8,
                            'uv' => 1,
                        ],
                    ]
                ]);
            })
        );

        City::factory()->create(['name' => 'Orlando', 'state' => 'FL']);

        $this->getJson('api/cities')
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    [
                        'city_id' => 1,
                        'city' => 'Orlando',
                        'state' => 'Florida',
                        'temp_c' => 25,
                        'temp_f' => 77,
                        'condition_text' => 'Clear',
                        'condition_icon' => '//cdn.weatherapi.com/weather/64x64/night/113.png',
                        'feelslike_c' => 26.6,
                        'feelslike_f' => 79.8,
                        'uv' => 1,
                    ],
                ]
            ]);
    }

    /** @test */
    public function it_adds_a_new_city(): void
    {
        $this->assertDatabaseCount('cities', 0);

        $this->postJson('api/cities', [
            'name' => 'Orlando',
            'state' => 'FL',
            ])
            ->assertCreated()
            ->assertJson([
                'data' => [
                    'name' => 'Orlando',
                    'state' => 'FL',
                ],
            ]);

        $this->assertDatabaseCount('cities', 1);
    }

    /**
     * @test
     * @dataProvider invalidCitiesData
     */
    public function it_gets_validation_error_with_invalid_requests_for_store($invalidRequest, $invalidFields): void
    {
        $this->postJson('api/cities', $invalidRequest)
            ->assertUnprocessable()
            ->assertJsonValidationErrors($invalidFields);

        $this->assertDatabaseCount('cities', 0);
    }

    /** @test */
    public function it_show_forecast_weather_for_a_city(): void
    {
        config()->set('services.weatherApi.baseUri', 'https://api.test.com');
        config()->set('services.weatherApi.apiKey', 'secret');

        $this->instance(
            WeatherInterface::class,
            Mockery::mock(WeatherApiService::class, function (MockInterface $mock) {
                $mock->shouldReceive('show')->once()
                    ->andReturn([
                        'data' => [
                            'city' => 'Orlando',
                            'state' => 'Florida',
                            'temp_c' => 31.7,
                            'temp_f' => 89.1,
                            'condition_text' => 'Partly cloudy',
                            'condition_icon' => '//cdn.weatherapi.com/weather/64x64/day/116.png',
                            'forecast' => [
                                [
                                    'date' => '2023-04-07',
                                    'maxtemp_c' => 36.4,
                                    'maxtemp_f' => 97.5,
                                    'mintemp_c' => 20.5,
                                    'mintemp_f' => 68.9,
                                    'avgtemp_c' => 26,
                                    'avgtemp_f' => 78.8,
                                    'condition_text' => 'Patchy rain possible',
                                    'condition_icon' => '//cdn.weatherapi.com/weather/64x64/day/176.png',
                                    'sunrise' => '07:09 AM',
                                    'sunset' => '07:47 PM'
                                ],
                                [
                                    'date' => '2023-04-08',
                                    'maxtemp_c' => 36.1,
                                    'maxtemp_f' => 97,
                                    'mintemp_c' => 18.9,
                                    'mintemp_f' => 66,
                                    'avgtemp_c' => 25.6,
                                    'avgtemp_f' => 78,
                                    'condition_text' => 'Patchy rain possible',
                                    'condition_icon' => '//cdn.weatherapi.com/weather/64x64/day/176.png',
                                    'sunrise' => '07:08 AM',
                                    'sunset' => '07:48 PM'
                                ],
                                [
                                    'date' => '2023-04-09',
                                    'maxtemp_c' => 21.6,
                                    'maxtemp_f' => 70.9,
                                    'mintemp_c' => 17.4,
                                    'mintemp_f' => 63.3,
                                    'avgtemp_c' => 20,
                                    'avgtemp_f' => 67.9,
                                    'condition_text' => 'Overcast',
                                    'condition_icon' => '//cdn.weatherapi.com/weather/64x64/day/122.png',
                                    'sunrise' => '07:07 AM',
                                    'sunset' => '07:48 PM'
                                ],
                                [
                                    'date' => '2023-04-10',
                                    'maxtemp_c' => 23.5,
                                    'maxtemp_f' => 74.3,
                                    'mintemp_c' => 17.2,
                                    'mintemp_f' => 63,
                                    'avgtemp_c' => 19.9,
                                    'avgtemp_f' => 67.9,
                                    'condition_text' => 'Moderate rain',
                                    'condition_icon' => '//cdn.weatherapi.com/weather/64x64/day/302.png',
                                    'sunrise' => '07:06 AM',
                                    'sunset' => '07:49 PM'
                                ],
                                [
                                    'date' => '2023-04-11',
                                    'maxtemp_c' => 24.3,
                                    'maxtemp_f' => 75.7,
                                    'mintemp_c' => 17,
                                    'mintemp_f' => 62.6,
                                    'avgtemp_c' => 20.3,
                                    'avgtemp_f' => 68.5,
                                    'condition_text' => 'Patchy rain possible',
                                    'condition_icon' => '//cdn.weatherapi.com/weather/64x64/day/176.png',
                                    'sunrise' => '07:05 AM',
                                    'sunset' => '07:49 PM'
                                ]
                            ]
                        ]
                    ]);
            })
        );

        City::factory()->create(['name' => 'Orlando', 'state' => 'FL']);

        $this->getJson('api/cities/1')
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'city' => 'Orlando',
                    'state' => 'Florida',
                    'temp_c' => 31.7,
                    'temp_f' => 89.1,
                    'condition_text' => 'Partly cloudy',
                    'condition_icon' => '//cdn.weatherapi.com/weather/64x64/day/116.png',
                    'forecast' => [
                        [
                            'date' => '2023-04-07',
                            'maxtemp_c' => 36.4,
                            'maxtemp_f' => 97.5,
                            'mintemp_c' => 20.5,
                            'mintemp_f' => 68.9,
                            'avgtemp_c' => 26,
                            'avgtemp_f' => 78.8,
                            'condition_text' => 'Patchy rain possible',
                            'condition_icon' => '//cdn.weatherapi.com/weather/64x64/day/176.png',
                            'sunrise' => '07:09 AM',
                            'sunset' => '07:47 PM'
                        ],
                        [
                            'date' => '2023-04-08',
                            'maxtemp_c' => 36.1,
                            'maxtemp_f' => 97,
                            'mintemp_c' => 18.9,
                            'mintemp_f' => 66,
                            'avgtemp_c' => 25.6,
                            'avgtemp_f' => 78,
                            'condition_text' => 'Patchy rain possible',
                            'condition_icon' => '//cdn.weatherapi.com/weather/64x64/day/176.png',
                            'sunrise' => '07:08 AM',
                            'sunset' => '07:48 PM'
                        ],
                        [
                            'date' => '2023-04-09',
                            'maxtemp_c' => 21.6,
                            'maxtemp_f' => 70.9,
                            'mintemp_c' => 17.4,
                            'mintemp_f' => 63.3,
                            'avgtemp_c' => 20,
                            'avgtemp_f' => 67.9,
                            'condition_text' => 'Overcast',
                            'condition_icon' => '//cdn.weatherapi.com/weather/64x64/day/122.png',
                            'sunrise' => '07:07 AM',
                            'sunset' => '07:48 PM'
                        ],
                        [
                            'date' => '2023-04-10',
                            'maxtemp_c' => 23.5,
                            'maxtemp_f' => 74.3,
                            'mintemp_c' => 17.2,
                            'mintemp_f' => 63,
                            'avgtemp_c' => 19.9,
                            'avgtemp_f' => 67.9,
                            'condition_text' => 'Moderate rain',
                            'condition_icon' => '//cdn.weatherapi.com/weather/64x64/day/302.png',
                            'sunrise' => '07:06 AM',
                            'sunset' => '07:49 PM'
                        ],
                        [
                            'date' => '2023-04-11',
                            'maxtemp_c' => 24.3,
                            'maxtemp_f' => 75.7,
                            'mintemp_c' => 17,
                            'mintemp_f' => 62.6,
                            'avgtemp_c' => 20.3,
                            'avgtemp_f' => 68.5,
                            'condition_text' => 'Patchy rain possible',
                            'condition_icon' => '//cdn.weatherapi.com/weather/64x64/day/176.png',
                            'sunrise' => '07:05 AM',
                            'sunset' => '07:49 PM'
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_updates_an_existing_city(): void
    {
        $city = City::factory()->create(['name' => 'Orlando', 'state' => 'FL']);

        $this->putJson("api/cities/$city->id", [
            'name' => 'New York',
            'state' => 'NY',
        ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    'name' => 'New York',
                    'state' => 'NY',
                ],
            ]);

        $city->refresh();

        $this->assertSame($city->name, 'New York');
        $this->assertSame($city->state, 'NY');
    }

    /**
     * @test
     * @dataProvider invalidCitiesData
     */
    public function it_gets_validation_error_with_invalid_requests_for_update($invalidRequest, $invalidFields): void
    {
        $city = City::factory()->create(['name' => 'Orlando', 'state' => 'FL']);

        $this->putJson("api/cities/$city->id", $invalidRequest)
            ->assertUnprocessable()
            ->assertJsonValidationErrors($invalidFields);

        $city->refresh();

        $this->assertSame($city->name, 'Orlando');
        $this->assertSame($city->state, 'FL');
    }

    /** @test */
    public function it_returns_not_found_when_updating_an_invalid_city(): void
    {
        $this->putJson("api/cities/1", [
            'name' => 'New York',
            'state' => 'NY',
        ])
            ->assertNotFound();
    }

    /** @test */
    public function it_deletes_an_existing_city(): void
    {
        $city = City::factory()->create(['name' => 'Orlando', 'state' => 'FL']);

        $this->assertDatabaseCount('cities', 1);

        $this->deleteJson("api/cities/$city->id")
            ->assertNoContent();

        $this->assertDatabaseCount('cities', 0);
    }

    /** @test */
    public function it_returns_not_found_when_deleting_an_invalid_city(): void
    {
        $this->deleteJson("api/cities/1")
            ->assertNotFound();
    }

    public static function invalidCitiesData(): array
    {
        return [
            [
                ['name' => '', 'state' => ''],
                ['name', 'state']
            ],
            [
                ['name' => '', 'state' => 'FL'],
                ['name']
            ],
            [
                ['name' => 'Orlando', 'state' => ''],
                ['state']
            ],
            [
                ['name' => 'Orlando', 'state' => ''],
                ['state']
            ],
            [
                ['name' => 'Orlando', 'state' => 'Florida'],
                ['state']
            ],
        ];
    }
}
