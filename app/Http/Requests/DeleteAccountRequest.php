<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'Please provide your password to delete your account.',
        ];
    }
}
