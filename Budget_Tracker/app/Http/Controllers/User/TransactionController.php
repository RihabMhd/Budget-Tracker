<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions.
     */
    public function index(Request $request)
    {
        $query = Transaction::with('category')
            ->where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $transactions = $query->paginate(15)->withQueryString();
        $categories   = Category::orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'categories'));
    }

    /**
     * Store a newly created transaction.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'          => ['required', 'in:Income,Expense'],
            'amount'        => ['required', 'numeric', 'min:0.01'],
            'description'   => ['required', 'string', 'max:255'],
            'category_id'   => ['required', 'exists:categories,id'],
            'date'          => ['required', 'date'],
            'receipt_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $path = null;
        if ($request->hasFile('receipt_image')) {
            $path = $request->file('receipt_image')->store('receipts', 'public');
        }

        Transaction::create([
            'user_id'             => Auth::id(),
            'category_id'         => $validated['category_id'],
            'type'                => $validated['type'],
            'amount'              => $validated['amount'],
            'description'         => $validated['description'],
            'date'                => $validated['date'],
            'receipt_image_path'  => $path,
        ]);

        // Award points for logging a transaction
        Auth::user()->increment('points', 5);
        Auth::user()->update(['last_activity' => now()]);

        return redirect()->back()->with('success', 'Transaction added successfully!');
    }

    /**
     * Show the form for editing a transaction.
     */
    public function edit(Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);
        $categories = Category::orderBy('name')->get();

        return view('transactions.edit', compact('transaction', 'categories'));
    }

    /**
     * Update a transaction.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);

        $validated = $request->validate([
            'type'          => ['required', 'in:Income,Expense'],
            'amount'        => ['required', 'numeric', 'min:0.01'],
            'description'   => ['required', 'string', 'max:255'],
            'category_id'   => ['required', 'exists:categories,id'],
            'date'          => ['required', 'date'],
            'receipt_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Handle receipt image replacement
        if ($request->hasFile('receipt_image')) {
            if ($transaction->receipt_image_path) {
                Storage::disk('public')->delete($transaction->receipt_image_path);
            }
            $validated['receipt_image_path'] = $request->file('receipt_image')->store('receipts', 'public');
        }

        // Handle receipt removal
        if ($request->boolean('remove_receipt') && $transaction->receipt_image_path) {
            Storage::disk('public')->delete($transaction->receipt_image_path);
            $validated['receipt_image_path'] = null;
        }

        $transaction->update([
            'type'                => $validated['type'],
            'amount'              => $validated['amount'],
            'description'         => $validated['description'],
            'category_id'         => $validated['category_id'],
            'date'                => $validated['date'],
            'receipt_image_path'  => $validated['receipt_image_path'] ?? $transaction->receipt_image_path,
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully!');
    }

    /**
     * Delete a transaction.
     */
    public function destroy(Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);

        if ($transaction->receipt_image_path) {
            Storage::disk('public')->delete($transaction->receipt_image_path);
        }

        $transaction->delete();

        return redirect()->back()->with('success', 'Transaction deleted.');
    }

    /**
     * Ensure the authenticated user owns this transaction.
     */
    private function authorizeTransaction(Transaction $transaction): void
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }
    }
}