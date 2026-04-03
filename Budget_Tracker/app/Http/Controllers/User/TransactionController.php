<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Category;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function __construct(protected TransactionService $transactionService) {}

    public function index(Request $request)
    {
        $transactions = Transaction::with('category')
            ->where('user_id', Auth::id())
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->date_from,   fn($q) => $q->where('date', '>=', $request->date_from))
            ->when($request->date_to,     fn($q) => $q->where('date', '<=', $request->date_to))
            ->when($request->search,      fn($q) => $q->where('description', 'like', "%{$request->search}%"))
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'categories'));
    }

    public function store(StoreTransactionRequest $request)
    {
        $this->transactionService->createTransaction(
            $request->validated(),
            $request->file('receipt_image')
        );

        if (!empty($result['newBadges'])) {
            $latest = end($result['newBadges']);
            return redirect()->route('transactions.index')
                ->with('success', 'Transaction saved!')
                ->with('badge_unlocked', $latest->title);
        }

        return redirect()->back()->with('success', 'Expense added successfully!');
    }

    public function edit(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) abort(403);

        $categories = Category::orderBy('name')->get();
        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) abort(403);

        $this->transactionService->updateTransaction(
            $transaction,
            $request->validated(),
            $request->file('receipt_image'),
            $request->boolean('remove_receipt')
        );

        return redirect()->route('transactions.index')->with('success', 'Expense updated!');
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) abort(403);

        $this->transactionService->deleteTransaction($transaction);

        return redirect()->back()->with('success', 'Expense deleted.');
    }
}
