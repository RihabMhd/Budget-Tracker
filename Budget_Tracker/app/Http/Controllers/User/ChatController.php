<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct(protected DashboardService $dashboardService) {}

    public function __invoke(Request $request)
    {
        $user = Auth::user();
        $userMessage = $request->input('message');

        $selectedMonth = now()->startOfMonth();
        $transactions = $this->dashboardService->getRecentTransactions($user->id, $selectedMonth);
        $categories = $this->dashboardService->getSpendingByCategory($user->id, $selectedMonth, 0);

        $context = "You are a helpful budget assistant for an app called MyBudget. 
                    The user's name is {$user->username}. 
                    Here is their recent spending data: \n";
        
        foreach ($categories as $cat) {
            $context .= "- {$cat['name']}: " . number_format($cat['amount'], 2) . " DH\n";
        }

        $context .= "\nRecent Transactions:\n";
        foreach ($transactions as $t) {
            $date = is_string($t->date) ? \Carbon\Carbon::parse($t->date) : $t->date;
            $context .= "- {$date->format('Y-m-d')}: {$t->description} ({$t->amount} DH)\n";
        }


        $response = Http::withoutVerifying()
            ->withToken(env('GROQ_API_KEY'))
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    ['role' => 'system', 'content' => $context],
                    ['role' => 'user', 'content' => $userMessage],
                ],
            ]);

        if ($response->failed()) {
            return response()->json(['reply' => "API Error: " . ($response->json()['error']['message'] ?? "Unknown error")]);
        }

        $data = $response->json();
        $reply = $data['choices'][0]['message']['content'] ?? "I'm sorry, I couldn't process that request.";

        return response()->json(['reply' => $reply]);
    }
}