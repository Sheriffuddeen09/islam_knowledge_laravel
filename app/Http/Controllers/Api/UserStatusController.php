<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserStatusController extends Controller
{
    public function checkStatus(Request $request)
    {
        // If user is logged in
        if (Auth::check()) {
            return response()->json([
                'status' => 'logged_in',
                'user' => Auth::user()
            ]);
        }

        // Optionally, check if the user email exists (registered) from query param
        if ($request->has('email')) {
            $exists = \App\Models\User::where('email', $request->email)->exists();
            if ($exists) {
                return response()->json(['status' => 'registered']);
            }
        }

        // Default: not registered
        return response()->json(['status' => 'not_registered']);
    }
}
