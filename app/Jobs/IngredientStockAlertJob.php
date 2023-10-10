<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\IngredientStockAlertNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IngredientStockAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private $ingredient)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $merchant = User::merchant()->first();

        $merchant->notify(new IngredientStockAlertNotification($this->ingredient));
    }
}
