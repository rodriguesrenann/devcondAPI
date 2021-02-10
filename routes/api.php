<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WallController;
use App\Http\Controllers\DocController;
use App\Http\Controllers\WarningController;
use App\Http\Controllers\BilletController;
use App\Http\Controllers\FoundAndLostController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\ReservationController;

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

Route::get('/ping', function(){
    return ['pong' => true];
});
//Rotas de autorizaçao

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

//Rotas depois de logado
Route::middleware('auth:api')->group(function(){
    Route::post('/auth/validate', [AuthController::class, 'validateToken']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    
    //User
    Route::get('/user', [UserController::class, 'getInfo']);
    Route::put('/user', [UserController::class, 'updateUser']);
    
    //Mural de avisos
    Route::get('/walls', [WallController::class, 'getAll']);
    Route::post('/wall/{id}/like', [WallController::class, 'toggleLike']);

    //Documentos
    Route::get('/docs', [DocController::class, 'getAll']);

    //Livro de ocorrencias
    Route::get('/warnings', [WarningController::class, 'getMyWarnings']);
    Route::post('/warning', [WarningController::class, 'setWarning']);
    Route::post('/warning/file', [WarningController::class, 'addWarningFile']);

    //Boletos
    Route::get('/billets', [BilletController::class, 'getAll']);

    //Achados e perdidos
    Route::get('/foundandlost', [FoundAndLostController::class, 'getAll']);
    Route::post('/foundandlost', [FoundAndLostController::class, 'insert']);
    Route::put('/foundandlost/{id}', [FoundAndLostController::class, 'update']);

    //Unidade
    Route::get('/unit/{id}', [UnitController::class, 'getInfo']);
    Route::post('/unit/{id}/addperson', [UnitController::class, 'addPerson']);
    Route::post('/unit/{id}/addvehicle', [UnitController::class, 'addVehicle']);
    Route::post('/unit/{id}/addpet', [UnitController::class, 'addPet']);
    Route::post('/unit/{id}/removeperson', [UnitController::class, 'removePerson']);
    Route::post('/unit/{id}/removevehicle', [UnitController::class, 'removeVehicle']);
    Route::post('/unit/{id}/removepet', [UnitController::class, 'removePet']);

    
    Route::get('/areas', [AreaController::class, 'getAllDates']);
    Route::get('/area/{id}/disableddates', [AreaController::class, 'getDisabledDates']);
    Route::get('/area/{id}/times', [AreaController::class, 'getTimes']);

    //Reservas
    Route::get('/myreservations', [ReservationController::class, 'getMyReservations']);
    Route::delete('/myreservations/{id}', [ReservationController::class, 'delReservation']);
    Route::post('/reservation/{id}', [ReservationController::class, 'setReservation']);
});