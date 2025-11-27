<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\OtpVerification;
use Illuminate\Database\QueryException;

class AuthController extends Controller
{
    // REGISTER USER
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'phone' => 'required|string|unique:users,phone',
            'phone_country_code' => 'required|string',
            'location' => 'required|string',
            'location_country_code' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'gender' => 'required|in:male,female,other',
            'role' => 'required|in:student,admin',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        }

        try {
            // Check OTP verification
            $otpRecord = OtpVerification::where('email', $request->email)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$otpRecord || !$otpRecord->verified) {
                return response()->json(['message' => 'Email not verified'], 400);
            }

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'dob' => $request->dob,
                'phone' => $request->phone,
                'phone_country_code' => $request->phone_country_code,
                'location' => $request->location,
                'location_country_code' => $request->location_country_code,
                'email' => $request->email,
                'gender' => $request->gender,
                'role' => $request->role,
                'password' => Hash::make($request->password),
                'email_verified_at' => now()
            ]);

            $otpRecord->delete();

            return response()->json([
                'status' => true,
                'message' => 'Registration complete',
                'user' => $user
            ], 201);

        } catch (QueryException $e) {
            if ($e->getCode() === '2002') {
                return response()->json(['message' => 'Server down, please try later'], 500);
            }
            return response()->json(['message' => 'Database error'], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    // LOGIN USER
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        try {
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

        } catch (QueryException $e) {
            if ($e->getCode() === '2002') {
                return response()->json(['message' => 'Server down, please try later'], 500);
            }
            return response()->json(['message' => 'Database error'], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
}
