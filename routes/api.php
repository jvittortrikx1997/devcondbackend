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

Route::get('auth/login', [AuthController::class, 'login']);
Route::get('auth/register', [AuthController::class, 'register']);

Route::middlware('auth:api')->group(function(){
    Route::post('auth/validate', [AuthController::class, 'validateToken']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    //Mural de Avisos
    Route::get('/walls', [WallController::class, 'getAll']);
    Route::post('/wall/{id}/like', [WallController::class, 'like']);

    // Documentos
    Route::get('/docs', [DocController::class, 'getAll']);

    //Livro de Ocorrencias
    Route::get('/warnings', [WarningController::class, 'getMyWarnings']);
    Route::post('/warnings', [WarningController::class, 'setWarning']);
    Route::post('/warning/file', [WarningController::class, 'setWarning']);



});

