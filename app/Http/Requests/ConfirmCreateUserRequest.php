<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmCreateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'password' => 'required|confirmed|min:6',
            'email' => 'required|email',
            'otp_code' => 'required|string',
            'otp_temp' => 'required|string'
        ];
    }
}
