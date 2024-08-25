<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'nullable|email'
        ];
    }
}
