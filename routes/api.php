<?php 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

Route::post('/send-otp', [RegisterController::class, 'sendOtp']);
Route::post('/verify-otp', [RegisterController::class, 'verifyOtp']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/check-email', function (Illuminate\Http\Request $request) {
    $exists = \App\Models\User::where('email', $request->email)->exists();

    return response()->json(['exists' => $exists]);
});
Route::post('/check-phone', function (Illuminate\Http\Request $request) {
    $exists = \App\Models\User::where('phone', $request->phone)->exists();

    return response()->json(['exists' => $exists]);
});

Route::post('/login-otp', [LoginController::class, 'loginSendOtp']);
Route::post('/login-verify', [LoginController::class, 'loginVerifyOtp']);
Route::post('/login-check', [LoginController::class, 'loginCheck']);
Route::post('/login', [LoginController::class, 'login']);

