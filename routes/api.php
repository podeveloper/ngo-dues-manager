<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\TestCardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::post('/invoices/{id}/pay', [PaymentController::class, 'pay']);
    Route::get('/test-cards', [TestCardController::class, 'index']);
});
