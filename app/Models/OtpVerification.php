<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // ✅ Correct namespace
use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    use HasFactory; // ✅ Trait is now correct

    protected $fillable = ['email', 'otp', 'expired_at', 'verified'];
    protected $dates = ['expired_at'];
}
