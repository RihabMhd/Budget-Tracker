<?php

namespace App\Http\Requests\Goal;

use Illuminate\Foundation\Http\FormRequest;

class GoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:255'
            ],
            'target_amount' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'numeric',
                'min:1'
            ],
            'current_amount' => [
                'nullable',
                'numeric',
                'min:0',
                'lte:target_amount' 
            ],
            'deadline' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'date',
                'after:today'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please give your goal a name.',
            'target_amount.min' => 'Your target amount must be at least 1 MAD.',
            'current_amount.lte' => 'Already saved amount cannot be higher than your target.',
            'deadline.after' => 'The deadline must be a date in the future.',
        ];
    }
}