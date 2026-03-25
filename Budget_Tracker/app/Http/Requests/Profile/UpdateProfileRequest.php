<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:28', Rule::unique('users')->ignore(Auth::id())],
            'email'    => ['required', 'email', Rule::unique('users')->ignore(Auth::id())],
        ];
    }
}