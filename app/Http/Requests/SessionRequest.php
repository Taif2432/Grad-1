<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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
            'communication_type' => 'required|in:chat,voice',
            'is_anonymous' => 'sometimes|boolean',
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
            'communication_type.in' => 'Session type must be either chat or voice.',
        ];
    }
}
