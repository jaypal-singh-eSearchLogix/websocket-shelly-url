<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {

//    $response = Http::withToken($accessToken)->get('https://<shelly_server>/device/all_status', [
//        'show_info' => 'true',
//        'no_shared' => 'true',
//    ]);
//
//    return $response->json();

    $accessToken = session('access_token'); // Fetch stored access token
    $webSocketService = new \App\Services\ShellyWebSocketService($accessToken);
    $webSocketService->connectToShellyWebSocket();

//    return view('welcome');
})->name('home');

Route::get('/shelly/authorize', [\App\Http\Controllers\ShellyController::class, 'redirectToProvider']);
Route::get('/shelly/callback', [\App\Http\Controllers\ShellyController::class, 'handleProviderCallback']);
