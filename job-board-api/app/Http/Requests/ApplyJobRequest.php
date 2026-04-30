<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ApplyJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('candidate');;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'resume' => 'required|mimes:pdf,doc,docx|max:2048',
            'cover_letter' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'resume.required' => 'The resume is required.',
            'resume.mimes' => 'The resume must be a PDF, DOC, or DOCX file.',
            'resume.max' => 'The resume must not exceed 2MB.',
            'cover_letter.string' => 'The cover letter must be a string.',
        ];
    }

    protected function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException('You must be a candidate to apply for this job.');
    }
}
