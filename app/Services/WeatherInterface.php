<?php

namespace App\Services;
use App\Models\City;
use Illuminate\Support\Collection;

interface WeatherInterface
{
    public function showMany(Collection $cities): array;

    public function show(City $city): array;
}
