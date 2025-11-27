<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CancelUnpaidOrders;

class RunCancelUnpaidOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-unpaid';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Cancel unpaid orders after timeout period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automatic cancellation of unpaid orders...');

        // Dispatch the job to cancel unpaid orders
        CancelUnpaidOrders::dispatch();

        $this->info('Job dispatched successfully. Unpaid orders will be cancelled.');
    }
}
