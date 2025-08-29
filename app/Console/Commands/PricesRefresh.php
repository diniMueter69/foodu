<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StoreProduct;
use App\Models\PriceSnapshot;

class PricesRefresh extends Command
{
    protected $signature = 'app:prices-refresh';
    protected $description = 'Create new price snapshots with small random drift (demo)';

    public function handle()
    {
        $count = 0;

        $sps = StoreProduct::with(['snapshots' => function ($q) {
            $q->orderByDesc('captured_at')->limit(1);
        }])->get();

        foreach ($sps as $sp) {
            $last = optional($sp->snapshots->first())->price;
            if ($last === null) continue;

            // ±5% Drift, mind. 0.05 CHF
            $drift = mt_rand(-5, 5) / 100;
            $new   = max(0.05, round(((float)$last) * (1 + $drift), 2));

            PriceSnapshot::create([
                'store_product_id' => $sp->id,
                'region'           => null,
                'price'            => $new,
                'captured_at'      => now(),
            ]);
            $count++;
        }

        $this->info("Created {$count} snapshots");
        \Log::info('prices-refresh', ['created' => $count]);
        return Command::SUCCESS;
    }
}
