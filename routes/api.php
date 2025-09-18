<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PlannerController;
use App\Http\Controllers\Api\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


// Authentikasi
Route::group([
    'prefix' => 'auth'
], function() {
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:api')->post('/logout', [AuthController::class, 'logout']);
});



// Role Admin
Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', 'AdminController@index');
});

// Role employee
Route::middleware(['auth:api', 'role:employee,admin'])->group(function () {
    
    Route::group([
        'prefix' => 'employee'
    ], function(){
        // Todo
        Route::prefix('todo')->group(function(){
            Route::get('/', [TodoController::class, 'index'])->name('employee.todo.index');
            Route::post('/', [TodoController::class, 'store'])->name('employee.todo.store');
            Route::put('/{id}', [TodoController::class, 'update'])->name('employee.todo.update');
            Route::delete('/{id}', [TodoController::class, 'destroy'])->name('employee.todo.delete');
        });

        Route::prefix('planner')->group(function(){
            Route::get('/', [PlannerController::class, 'index'])->name('employee.planner.index');
            Route::post('/', [PlannerController::class, 'store'])->name('employee.planner.store');
            Route::put('/{id}', [PlannerController::class, 'update'])->name('employee.planner.update');
            Route::delete('/{id}', [PlannerController::class, 'destroy'])->name('employee.planner.delete');
            
        });
    });
});

// kalau mau multi role
Route::middleware(['auth:api', 'role:admin,employee'])->group(function () {
    Route::get('/common/dashboard', 'CommonController@index');
});
