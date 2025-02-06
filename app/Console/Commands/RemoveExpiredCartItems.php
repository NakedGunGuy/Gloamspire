<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RemoveExpiredCartItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-expired-cart-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove cart items that are older than 1 day';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredItems = Cart::where('created_at', '<', Carbon::now()->subDay())->get();

        foreach ($expiredItems as $cartItem) {
            $listing = $cartItem->listing;
            if ($listing) {
                $listing->card_count += $cartItem->amount;
                $listing->save();
            }

            $cartItem->delete();
        }

        $this->info("Expired cart items removed successfully.");
        return 0;
    }
}
