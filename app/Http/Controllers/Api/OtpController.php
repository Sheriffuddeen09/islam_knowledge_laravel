<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        // Your OTP logic
        return response()->json([
            'message' => 'OTP sent successfully',
        ]);
    }
}

