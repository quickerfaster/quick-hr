<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Admin\Http\Controllers\AuthController;


use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;



Route::middleware([
    // InitializeTenancyByDomain::class,
    // PreventAccessFromCentralDomains::class,
])->group(function () {

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('admin/login', [AuthController::class, 'login']);
    Route::post('admin/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    /*Route::post('/test', function () {
        //return $controller->store($request);
        return json_encode('api route test');
    });*/

});



// API routes for App\Modules\Admin\Http\Controllers\Api\ActivityLogController
Route::apiResource('activity-logs', App\Modules\Admin\Http\Controllers\Api\ActivityLogController::class)
    ->names('admin.activity_log');

// API routes for App\Modules\Admin\Http\Controllers\Api\JobTitleController
Route::apiResource('job-titles', App\Modules\Admin\Http\Controllers\Api\JobTitleController::class)
    ->names('admin.job_title');
