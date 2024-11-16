<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\RetailStoreController;
use App\Http\Controllers\RaffleController;
use App\Http\Middleware\ApiAuthentication;
use App\Http\Controllers\CustomerRegistration;

Route::group(['middleware' => ['web']], function () {


    Route::middleware([ApiAuthentication::class])->group(function () {

        Route::post('/generate-qr-code', [QrCodeController::class, 'generate']);
        Route::get('/get-qr-code-generated', [QrCodeController::class, 'getqrcodegenerated']);
        Route::post('/delete-generate-qr-code', [QrCodeController::class, 'deletegeneratedqr']);
        Route::get('/get-queue-status', [QrCodeController::class, 'queueProgress']);
        Route::post('/export-qr', [QrCodeController::class, 'exportQR']);

        // Retail Store Controller
        Route::post('/add-retail-store', [RetailStoreController::class, 'addcluster']);
        Route::get('/get-cluster', [RetailStoreController::class, 'getcluster']);
        Route::post('/cluster-status', [RetailStoreController::class, 'clusterstatus']);
        Route::post('/add-store', [RetailStoreController::class, 'addstore']);
        Route::post('/update-store', [RetailStoreController::class, 'updatestore']);
        Route::get('/get-all-store', [RetailStoreController::class, 'getallstore']);
        Route::get('/remove-retail', [RetailStoreController::class, 'removeretailstore']);
        Route::post('/upload-retail-store', [RetailStoreController::class, 'uploadcsv']);

        //Raffle
        Route::post('/get-raflle-entry', [RaffleController::class, 'getraffleentry']);
        Route::post('/raffle-draw', [RaffleController::class, 'raffledraw']);
        Route::get('/get-all-winner', [RaffleController::class, 'getallwinner']);
        Route::get('/get-all-entry', [RaffleController::class, 'getallentry']);
        Route::get('/get-all-event', action: [RaffleController::class, 'getallevent']);
        Route::post('/add-event', [RaffleController::class, 'addevent']);
        Route::post('/raffle-redraw', [RaffleController::class, 'redraw']);
        Route::post('/update-event', [RaffleController::class, 'updateevent']);
        Route::post('/inactive-event', [RaffleController::class, 'inactiveevent']);
        Route::post('/event-winner', [RaffleController::class, 'geteventwinner']);
        Route::post('/event-selected', [RaffleController::class, 'getaselectedevent']);

        //Admin Details
        Route::get('/get-admin-details', [AuthenticationController::class, 'getadmindetails']);
        Route::post('/admin-changepassword', [AuthenticationController::class, 'changepassword']);
        Route::post('/update-admin-details', [AuthenticationController::class, 'updateadmin']);
    });

    //Authentication
    Route::post('/admin/auth', [AuthenticationController::class, 'signin']);

    Route::get('/get-auth', [AuthenticationController::class, 'getauth']);
    Route::post('/verify-user', [AuthenticationController::class, 'verifyuser']);
    Route::post('/logout', [AuthenticationController::class, 'logout']);
    Route::post('/resend-code', [AuthenticationController::class, 'resendcode']);

    //Customer Api
    Route::post('/register-raffle-entry', [CustomerRegistration::class, 'register']);


});
