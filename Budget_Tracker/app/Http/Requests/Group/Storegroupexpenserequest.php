<?php

namespace App\Http\Requests\Group;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreGroupExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var \App\Models\Group $group */
        $group = $this->route('group');

        return $group->members()->where('user_id', Auth::id())->exists();
    }

    public function rules(): array
    {
        return [
            'amount'      => ['required', 'numeric', 'min:0.01'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string', 'max:255'],
            'date'        => ['nullable', 'date'],
        ];
    }
}