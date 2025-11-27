<?php
// app/Http/Controllers/Auth/RegisterController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\OtpVerification;
use Carbon\Carbon;
use App\Mail\OtpMail;

class RegisterController extends Controller
{
    // Send OTP
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if($validator->fails()){
            return response()->json(["errors"=> $validator->errors()], 422);
        }

        $email = $request->email;
        $otp = rand(100000, 999999);
        $expired = Carbon::now()->addMinutes(10);

        // Remove old OTPs
        OtpVerification::where('email', $email)->delete();

        // Save new OTP
        OtpVerification::create([
            'email' => $email,
            'otp' => Hash::make($otp),
            'expired_at' => $expired,
            'verified' => false,
        ]);

        Mail::to($email)->send(new OtpMail($otp));

        return response()->json([
            'message' => 'OTP sent successfully.',
            'expires_at' => $expired
        ]);
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp'   => 'required'
        ]);

        if($validator->fails()){
            return response()->json(["errors"=> $validator->errors()], 422);
        }

        $record = OtpVerification::where('email', $request->email)
            ->orderBy('created_at','desc')
            ->first();

        if(!$record){
            return response()->json(['message'=>'No OTP requested for this email.'], 404);
        }

        if($record->verified){
            return response()->json(['message'=>'OTP already verified.'], 400);
        }

        if(Carbon::now()->greaterThan($record->expired_at)){
            return response()->json(['message'=>'OTP expired.'], 400);
        }

        if(!Hash::check($request->otp, $record->otp)){
            return response()->json(['message'=>'Invalid OTP.'], 400);
        }

        $record->verified = true;
        $record->save();

        return response()->json(['message'=>'OTP verified successfully.']);
    }

    // Register
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

        if($validator->fails()){
            return response()->json(["errors"=> $validator->errors()], 422);
        }

        // Check OTP
        $otpRecord = OtpVerification::where('email', $request->email)
            ->orderBy('created_at','desc')->first();

        if(!$otpRecord || !$otpRecord->verified){
            return response()->json(['message'=>'Email not verified'], 400);
        }

        // Create user
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

        // Remove OTP
        $otpRecord->delete();

        return response()->json([
            'status' => true,
            'message'=>'Registration complete', 
            'user' => $user], 
            201);
    }
}
