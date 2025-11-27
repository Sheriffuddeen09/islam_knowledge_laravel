<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    // -----------------------------
    // SEND OTP
    // -----------------------------
    public function loginSendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email not found'], 404);
        }

        $otp = rand(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'token' => null,       // avoid null error
                'created_at' => now()
            ]
        );

        Mail::raw("Your OTP code is $otp", function ($message) use ($request) {
            $message->to($request->email)->subject('Your Login OTP Code');
        });

        return response()->json(['message' => 'OTP sent']);
    }


      // -----------------------------
    // CHECK LOGIN USER IN
    // -----------------------------

    public function loginCheck(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string'
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'Email not found'], 404);
    }

    if (!\Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Incorrect password'], 401);
    }

    return response()->json(['message' => 'OK'], 200);
}


    // -----------------------------
    // VERIFY OTP AND LOG USER IN
    // -----------------------------
    public function loginVerifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric'
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Invalid OTP Code'], 422);
        }

        // OTP valid → log the user in
        $user = User::where('email', $request->email)->first();

        Auth::login($user);

        // create Sanctum token
        $token = $user->createToken('otp_login')->plainTextToken;

        return response()->json([
            'message' => 'OTP Verified, Login Successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    // -----------------------------
    // NORMAL EMAIL + PASSWORD LOGIN
    // -----------------------------
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid login'], 401);
        }

        $token = $request->user()->createToken('login_token')->plainTextToken;

        return response()->json([
            'message' => 'Login Successful',
            'token' => $token,
            'user' => $request->user()
        ]);
    }
}
