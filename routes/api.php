<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BilletController;
use App\Http\Controllers\DocController;
use App\Http\Controllers\FoundAndLostController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WallController;
use App\Http\Controllers\WarningController;


Route::get('/ping', function(){
    return ['pong' => true];
});

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function(){
    Route::post('auth/validate', [AuthController::class, 'validateToken']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/walls', [WallController::class, 'getAll']);
    Route::post('/wall/{id}/like', [WallController::class, 'like']);

    Route::get('/docs', [DocController::class, 'getAll']);

    Route::get('/warnings', [WarningController::class, 'getMyWarnings']);
    Route::post('/warning', [WarningController::class, 'setWarning']);
    Route::post('/warning/file', [WarningController::class, 'addWarningFile']);

    Route::get('/billets', [BilletController::class, 'getAll']);

    Route::get('/foundandlost', [FoundAndLostController::class, 'getAll']);
    Route::post('/foundandlost', [FoundAndLostController::class, 'insert']);
    Route::put('/foundandlost/{id}', [FoundAndLostController::class, 'update']);

    Route::get('/unit/{id}', [UnitController::class, 'getInfo']);
    Route::post('/unit/{id}/addPerson', [UnitController::class, 'addPerson']);
    Route::post('/unit/{id}/addVehicle', [UnitController::class, 'addVehicle']);
    Route::post('/unit/{id}/addPet', [UnitController::class, 'addPet']);
    Route::post('/unit/{id}/removePerson', [UnitController::class, 'removePerson']);
    Route::post('/unit/{id}/removeVeiculo', [UnitController::class, 'removeVeiculo']);
    Route::post('/unit/{id}/removePet', [UnitController::class, 'removePet']);

    Route::get('/reservations', [ReservationController::class, 'getReservation']);
    Route::post('/reservation/{id}', [ReservationController::class, 'setReservation']);

    Route::get('/reservation/{id}/disableddates', [ReservationController::class, 'getDisabledDates']);
    Route::get('/reservation/{id}/times', [ReservationController::class, 'getTimes']);

    Route::get('/myreservations', [ReservationController::class, 'getMyreservations']);
    Route::delete('/myreservations/{id}', [ReservationController::class, 'delMyreservations']);


});

