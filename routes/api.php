<?php

use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PaymentController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', function (Illuminate\Http\Request $request) {
    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Hatalı giriş'], 401);
    }

    return response()->json([
        'token' => $user->createToken('web-app')->plainTextToken
    ]);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::post('/invoices/{id}/pay', [PaymentController::class, 'pay']);
    Route::get('/test-cards', [App\Http\Controllers\Api\TestCardController::class, 'index']);
});
