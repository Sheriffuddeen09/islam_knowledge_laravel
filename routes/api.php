<?php 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Api\UserStatusController;
use App\Http\Controllers\Auth\LoginController;

// Status Api Route
use Illuminate\Http\Request;

Route::middleware('auth:sanctum')->get('/user-status', function(Request $request) {
    $user = $request->user();
    if ($user) {
        return response()->json([
            'status' => 'logged_in',
            'user' => $user
        ]);
    }

    // If user has started registration but not logged in
    return response()->json(['status' => 'registered']);
});


Route::get('/user-status', [UserStatusController::class, 'checkStatus']);
// routes/api.php
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json(['user' => $request->user()]);
});


// Register Api Route
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

Route::post('/check', [RegisterController::class, 'checkBeforeNext']);


// Login Api Route
Route::post('/login-otp', [LoginController::class, 'loginSendOtp']);
Route::post('/login-verify', [LoginController::class, 'loginVerifyOtp']);
Route::post('/login-check', [LoginController::class, 'loginCheck']);
Route::post('/login', [LoginController::class, 'login']);

