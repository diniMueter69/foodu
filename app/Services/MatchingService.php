<?php

namespace App\Services;

use App\Models\Product;

class MatchingService
{
    /**
     * Sehr einfacher Matcher: findet ein Product per LIKE
     * und hängt Bestpreis aus PriceService an.
     */
    public function resolve(string $ingredient, ?int $storeId = null, ?string $region = null): ?array
    {
        $term = trim($ingredient);
        if ($term === '') return null;

        // grober Textmatch
        $p = Product::where('name', 'like', '%'.$term.'%')
            ->orderByRaw('CASE WHEN name = ? THEN 0 WHEN name LIKE ? THEN 1 ELSE 2 END, LENGTH(name)', [$term, $term.'%'])
            ->first();

        if (!$p) return null;

        // Bestpreis (über alle Stores); Region/Store könnte man später einbauen
        $ps = app(\App\Services\PriceService::class);
        $best = $ps->bestPrice($p->id, $region);

        return [
            'product_id'  => $p->id,
            'name'        => $p->name,
            'unit'        => $p->unit,
            'size'        => (int)$p->size,
            'best_price'  => $best['price'] ?? null,
            'best_store'  => $best['store'] ?? null,
            'captured_at' => $best['captured_at'] ?? null,
        ];
    }
}
