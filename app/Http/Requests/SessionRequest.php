<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'professional_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $user = \App\Models\User::find($value);
                    if (!$user || !in_array($user->role, ['psychologist', 'volunteer'])) {
                        $fail('The selected professional must be a psychologist or volunteer.');
                    }
                },
            ],
            'scheduled_at' => 'required|date|after:now',
            'session_type' => 'required|in:chat,voice',
        ];
    }

    public function messages(): array
    {
        return [
            'professional_id.required'  => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $user = \App\Models\User::find($value);
                    if (!$user || !in_array($user->role, ['psychologist', 'volunteer'])) {
                        $fail('The selected professional must be a psychologist or a volunteer.');
                    }
                },],
            'scheduled_at.after' => 'The scheduled time must be in the future.',
            'session_type.in' => 'Session type must be either chat or voice.',
        ];
    }
}
