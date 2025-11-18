<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifySellerTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:seller-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify seller transaction data has been added correctly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $transactions = DB::table('seller_transactions')
            ->join('sellers', 'seller_transactions.seller_id', '=', 'sellers.id')
            ->select(
                'seller_transactions.id',
                'sellers.store_name',
                'seller_transactions.transaction_type',
                'seller_transactions.amount',
                'seller_transactions.balance_before',
                'seller_transactions.balance_after',
                'seller_transactions.description',
                'seller_transactions.transaction_date'
            )
            ->orderBy('seller_transactions.seller_id')
            ->orderBy('seller_transactions.created_at')
            ->get();

        $this->info("Seller Transactions:");
        $this->newLine();

        foreach ($transactions as $transaction) {
            $this->line("ID: {$transaction->id}");
            $this->line("  Store: {$transaction->store_name}");
            $this->line("  Type: {$transaction->transaction_type}");
            $this->line("  Amount: Rp " . number_format($transaction->amount, 2));
            $this->line("  Balance Before: Rp " . number_format($transaction->balance_before, 2));
            $this->line("  Balance After: Rp " . number_format($transaction->balance_after, 2));
            $this->line("  Description: {$transaction->description}");
            $this->line("  Date: {$transaction->transaction_date}");
            $this->newLine();
        }

        $this->info("Total transactions: " . $transactions->count());
    }
}
