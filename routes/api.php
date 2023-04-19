<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\EmployeeController;
use App\Models\Employee;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::post('/auth/register', [AuthController::class, 'register']);

Route::post('/auth/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/branchname/{no_branch}', [BranchController::class, 'branchname']);
    Route::get('/branchemployees/{branch}', [BranchController::class, 'branchemployees']);

    Route::get('branchs', [BranchController::class, 'index']);
    Route::post('branch', [BranchController::class, 'store']);
    Route::get('branch/{id}', [BranchController::class, 'show']);
    
    
    Route::get('employee', [EmployeeController::class, 'index']);
    Route::post('employee', [EmployeeController::class, 'store']);
    Route::get('employee/{id}', [EmployeeController::class, 'show']);


    Route::post('/auth/logout', [AuthController::class, 'logout']);
});