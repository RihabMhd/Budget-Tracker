<?php 
namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'type'          => ['required', 'in:Income,Expense'],
            'amount'        => ['required', 'numeric', 'min:0.01'],
            'description'   => ['required', 'string', 'max:255'],
            'category_id'   => ['required', 'exists:categories,id'],
            'date'          => ['required', 'date'],
            'receipt_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}