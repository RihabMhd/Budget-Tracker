<?php 
namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->transaction->user_id === Auth::id();
    }

    public function rules(): array
    {
        return [
            'type'           => ['required', 'in:Income,Expense'],
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'description'    => ['required', 'string', 'max:255'],
            'category_id'    => ['required', 'exists:categories,id'],
            'date'           => ['required', 'date'],
            'receipt_image'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_receipt' => ['nullable', 'boolean'],
        ];
    }
}