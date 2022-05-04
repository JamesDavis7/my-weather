<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/weather', function () {
    $api_key = config('services.openweather.key');
    $lat = request('lat');
    $lon = request('lon');

    $response = Http::get("https://api.openweathermap.org/data/2.5/onecall?&units=metric&lat=$lat&lon=$lon&appid=$api_key");
    return $response->json();
});

