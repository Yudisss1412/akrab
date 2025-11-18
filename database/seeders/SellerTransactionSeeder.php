<?php

namespace Database\Seeders;

use App\Models\Seller;
use App\Models\SellerTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SellerTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sellers = Seller::all();

        foreach ($sellers as $seller) {
            // Generate some dummy transactions for each seller
            $transactions = [
                // Sales transactions
                [
                    'transaction_type' => 'sale',
                    'amount' => 150000.00,
                    'balance_before' => $seller->balance - 150000.00,
                    'balance_after' => $seller->balance,
                    'reference_type' => 'order',
                    'reference_id' => rand(1, 10),
                    'description' => 'Penjualan produk - Order #' . rand(1, 10),
                    'status' => 'completed',
                    'transaction_date' => now()->subDays(rand(1, 30))
                ],
                [
                    'transaction_type' => 'sale',
                    'amount' => 320000.00,
                    'balance_before' => $seller->balance - 470000.00,
                    'balance_after' => $seller->balance - 150000.00,
                    'reference_type' => 'order',
                    'reference_id' => rand(1, 10),
                    'description' => 'Penjualan produk - Order #' . rand(1, 10),
                    'status' => 'completed',
                    'transaction_date' => now()->subDays(rand(1, 30))
                ],
                // Fee transaction
                [
                    'transaction_type' => 'fee',
                    'amount' => -25000.00,
                    'balance_before' => $seller->balance - 445000.00,
                    'balance_after' => $seller->balance - 470000.00,
                    'reference_type' => 'platform_fee',
                    'reference_id' => null,
                    'description' => 'Biaya platform bulanan',
                    'status' => 'completed',
                    'transaction_date' => now()->subDays(rand(1, 30))
                ],
                // Withdrawal transaction
                [
                    'transaction_type' => 'withdrawal',
                    'amount' => -100000.00,
                    'balance_before' => $seller->balance - 345000.00,
                    'balance_after' => $seller->balance - 445000.00,
                    'reference_type' => 'withdrawal',
                    'reference_id' => rand(1, 5),
                    'description' => 'Permintaan penarikan dana',
                    'status' => 'completed',
                    'transaction_date' => now()->subDays(rand(1, 30))
                ]
            ];

            foreach ($transactions as $transaction) {
                // Make sure we don't generate negative balances
                if ($transaction['balance_before'] < 0 || $transaction['balance_after'] < 0) {
                    continue;
                }

                SellerTransaction::create([
                    'seller_id' => $seller->id,
                    'transaction_type' => $transaction['transaction_type'],
                    'amount' => $transaction['amount'],
                    'balance_before' => $transaction['balance_before'] > 0 ? $transaction['balance_before'] : 0,
                    'balance_after' => $transaction['balance_after'] > 0 ? $transaction['balance_after'] : 0,
                    'reference_type' => $transaction['reference_type'],
                    'reference_id' => $transaction['reference_id'],
                    'description' => $transaction['description'],
                    'status' => $transaction['status'],
                    'transaction_date' => $transaction['transaction_date'],
                ]);
            }
            
            // Also add some more recent transactions
            for ($i = 0; $i < 3; $i++) {
                $amount = rand(100000, 500000);
                $lastTransaction = SellerTransaction::where('seller_id', $seller->id)->orderBy('created_at', 'desc')->first();
                $balanceBefore = $lastTransaction ? $lastTransaction->balance_after : $seller->balance;
                
                SellerTransaction::create([
                    'seller_id' => $seller->id,
                    'transaction_type' => 'sale',
                    'amount' => $amount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceBefore + $amount,
                    'reference_type' => 'order',
                    'reference_id' => rand(1, 20),
                    'description' => 'Penjualan produk terbaru - Order #' . rand(11, 30),
                    'status' => 'completed',
                    'transaction_date' => now()->subDays($i),
                ]);
            }
        }

        echo "Seller transaction data has been added successfully.\n";
    }
}