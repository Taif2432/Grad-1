<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
 
    public function rules(): array
    {
        return [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
    
        ];
    }
}
