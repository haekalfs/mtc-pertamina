<?php

use App\Helpers\JwtHelper;
use App\Http\Controllers\OperationController;
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

Route::get('/generate-token', function () {
    $token = JwtHelper::generateToken(['user' => 'trusted-client'], 5); // expires in 5 min

    return response()->json([
        'success' => true,
        'token' => $token,
        'expires_in' => 300
    ]);
});

Route::get('/sendMasterDataTraining/{year}', [OperationController::class, 'sendMasterDataTraining'])
    ->middleware('verify.domain.jwt');
