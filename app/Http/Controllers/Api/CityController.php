<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CityRequest;
use App\Models\City;
use App\Services\WeatherInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CityController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private readonly WeatherInterface $weatherService,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $weathers = $this->weatherService->showMany(City::all());
        return response()->json($weathers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CityRequest $request): JsonResponse
    {
        $city = City::create($request->safe()->only(['name', 'state']));
        return response()->json(['data' => $city], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(City $city): JsonResponse
    {
        $weather = $this->weatherService->show($city);
        return response()->json($weather);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CityRequest $request, City $city): JsonResponse
    {
        $city->update($request->safe()->only(['name', 'state']));
        return response()->json(['data' => $city]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city): JsonResponse
    {
        $city->delete();
        return response()->json(['data' => []], Response::HTTP_NO_CONTENT);
    }
}
