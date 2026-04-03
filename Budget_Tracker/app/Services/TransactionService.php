<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Services\BadgeService;

class TransactionService
{
    protected $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
    }

    public function createTransaction(array $data, $file = null): array
    {
        if ($file) {
            $data['receipt_image_path'] = $file->store('receipts', 'public');
        }

        unset($data['type']);

        $transaction = Transaction::create($data + ['user_id' => Auth::id()]);

        $user = Auth::user();
        $user->increment('points', 5);
        $user->update(['last_activity' => now()]);

        $newBadges = $this->badgeService->checkAndAward($user);

        return [
            'transaction' => $transaction,
            'newBadges' => $newBadges
        ];
    }

    public function updateTransaction(Transaction $transaction, array $data, $file = null, bool $removeReceipt = false): Transaction
    {
        unset($data['type']); // type column removed

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

    public function deleteTransaction(Transaction $transaction): bool
    {
        $this->deleteReceipt($transaction->receipt_image_path);
        return $transaction->delete();
    }

    protected function deleteReceipt(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
