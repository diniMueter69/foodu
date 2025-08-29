<?php

namespace App\Services;

use App\Models\StoreProduct;

class PriceService
{
    /**
     * Liefert den besten Preis für ein Produkt (einfacher MVP).
     * @return array|null ['store'=>string,'store_id'=>int,'price'=>float,'captured_at'=>string]
     */
    public function bestPrice(int $productId, ?string $region = null): ?array
    {
        $offers = StoreProduct::with([
                'store',
                'snapshots' => fn($q) => $q->orderByDesc('captured_at')->limit(1)
            ])
            ->where('product_id', $productId)
            ->get();

        $best = null;
        foreach ($offers as $o) {
            $snap = $o->snapshots->first();
            if (!$snap) continue;
            $price = (float) $snap->price;

            if ($best === null || $price < $best['price']) {
                $best = [
                    'store'       => $o->store?->name,
                    'store_id'    => $o->store_id,
                    'price'       => $price,
                    'captured_at' => $snap->captured_at->toDateTimeString(),
                ];
            }
        }
        return $best;
    }
}

