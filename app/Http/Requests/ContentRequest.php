<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = in_array($this->method(), ['PUT', 'PATCH']);

        return [
            'title' => $isUpdate ? 'sometimes|string|max:255' : 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:article,video,pdf',
            'content_type_id' => 'required|exists:content_types,id',
            'file' => 'nullable|file|mimes:pdf,mp4,docx,webm,avi|max:10240',
        ];
    }
}
