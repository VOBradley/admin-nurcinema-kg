<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOtp extends Model
{
    protected $fillable = ['otp_code', 'otp_temp', 'otp_model', 'otp_type'];
}
