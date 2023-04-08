<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\API\ItemsController;
use App\Http\Controllers\API\RequisitionsController;
use App\Http\Controllers\API\SuppliersController;
use App\Http\Controllers\API\ReceivesController;
use App\Http\Controllers\API\IssuesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();

//     // if (Gate::allows('view', $item)) {
//     //     // user is authorized to view the item
//     // }

//     // $this->authorize('update', $item);
// });
Route::post('/auth/register', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']); 
// Route::apiResource('items', ItemsController::class)->middleware('auth:sanctum');


Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Routes for admin role
    Route::put('requisitions/update_status/{id}', [RequisitionsController::class,'updateStatus']);
    Route::apiResource('items', ItemsController::class);
    Route::apiResource('suppliers', SuppliersController::class);
});
Route::middleware(['auth:sanctum', 'role:employee,admin,store-executive'])->group(function () {
    Route::get('requisitions/get_approved', [RequisitionsController::class,'getApprovedList']);
    Route::get('items', [ItemsController::class,'index']);
    Route::get('suppliers', [SuppliersController::class,'index']);
});

Route::middleware(['auth:sanctum', 'role:employee,admin'])->group(function () {
    Route::get('requisitions/get_new', [RequisitionsController::class,'getNewList']);
});
Route::middleware(['auth:sanctum', 'role:employee'])->group(function () {
    // dd(Auth::user());
    // Routes for employee role
    // Route::post('requisitions', [RequisitionsController::class, 'store']);
    Route::get('requisitions/get_rejected', [RequisitionsController::class,'getRejectedList']);
    Route::apiResource('requisitions', RequisitionsController::class);


    // Route::get('requisitions', [RequisitionsController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'role:store-executive'])->group(function () {
    // Routes for store executive role
    // Route::post('receives', [ReceiveController::class, 'store']);
    // Route::get('receives', [ReceiveController::class, 'index']);
    Route::get('receives/stock', [ReceivesController::class,'getStock']);
    Route::apiResource('issues', IssuesController::class);
    Route::apiResource('receives', ReceivesController::class);
});