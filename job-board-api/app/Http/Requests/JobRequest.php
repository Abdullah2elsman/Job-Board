<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class JobRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'title' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'max:255'
            ],
            'description' => [
                $isUpdate ? 'sometimes' : 'required',
                'string'
            ],
            'location' => [
                $isUpdate ? 'sometimes' : 'required',
                'string'
            ],
            'type' => [
                $isUpdate ? 'sometimes' : 'required',
                'in:full-time,part-time,remote,contract'
            ],
            'category_id' => [
                $isUpdate ? 'sometimes' : 'required',
                'exists:categories,id'
            ],
            'salary' => 'nullable|numeric',
        ];
    }
}
