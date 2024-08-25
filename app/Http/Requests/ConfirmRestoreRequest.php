<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmRestoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'otp_code' => 'required|string',
            'otp_temp' => 'required|string'
        ];
    }
}
