<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        // return true;
        return auth()->check() && auth()->user()->role === 'psychologist';
    }

    public function rules(): array
    {
        return [
            'available_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ];
    }

    public function messages(): array
    {
        return [
            'available_date.required' => 'The availability date is required.',
            'available_date.date' => 'Please provide a valid date.',
            'available_date.after_or_equal' => 'Availability must be for today or a future date.',
            'start_time.required' => 'The start time is required.',
            'start_time.date_format' => 'Start time must be in the format HH:MM.',
            'end_time.required' => 'The end time is required.',
            'end_time.date_format' => 'End time must be in the format HH:MM.',
            'end_time.after' => 'End time must be after the start time.',
        ];
    }
}

