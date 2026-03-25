<?php 
namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class TransactionService
{
    public function createTransaction(array $data, $file = null)
    {
        if ($file) {
            $data['receipt_image_path'] = $file->store('receipts', 'public');
        }
        $transaction = Transaction::create($data + ['user_id' => Auth::id()]);
        $user = Auth::user();
        $user->increment('points', 5);
        $user->update(['last_activity' => now()]);

        return $transaction;
    }

    public function updateTransaction(Transaction $transaction, array $data, $file = null, bool $removeReceipt = false)
    {
        if ($file) {
            $this->deleteReceipt($transaction->receipt_image_path);
            $data['receipt_image_path'] = $file->store('receipts', 'public');
        } elseif ($removeReceipt) {
            $this->deleteReceipt($transaction->receipt_image_path);
            $data['receipt_image_path'] = null;
        }

        $transaction->update($data);
        return $transaction;
    }

    public function deleteTransaction(Transaction $transaction)
    {
        $this->deleteReceipt($transaction->receipt_image_path);
        return $transaction->delete();
    }

    protected function deleteReceipt(?string $path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}