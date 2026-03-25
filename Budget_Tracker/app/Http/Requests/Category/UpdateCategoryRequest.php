<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:28'],
            'color' => ['required', 'string', 'max:28', 'regex:/^#[0-9A-Fa-f]{3,6}$/'],
        ];
    }
}