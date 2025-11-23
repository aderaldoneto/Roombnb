<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\RoomController as ApiRoomController;
use App\Http\Controllers\Api\V1\ReservationController as ApiReservationController;

Route::prefix('v1')->group(function () {

    Route::get('/rooms', [ApiRoomController::class, 'index']);
    Route::get('/rooms/{room}', [ApiRoomController::class, 'show'])
        ->whereNumber('room');
    Route::post('/rooms/{room}/reservations', [ApiReservationController::class, 'store'])
        ->whereNumber('room');
    Route::get('/reservations', [ApiReservationController::class, 'index']);
});
