<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

use App\Models\Recipe;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\Plan;
use App\Models\PlanMeal;

// Products & Prices
use App\Models\Product;
use App\Models\StoreProduct;
use App\Models\PriceSnapshot;
use App\Services\PriceService;
use App\Services\MatchingService;

Route::get('/health', fn () => ['ok' => true]);

// =======================
// Rezepte
// =======================
Route::get('/recipes', fn () =>
    Recipe::select('id','title','price_per_portion','time','kcal')
          ->orderBy('id')
          ->get()
);

Route::get('/recipes/{recipe}', fn (Recipe $recipe) => [
    'id' => $recipe->id,
    'title' => $recipe->title,
    'price_per_portion' => (float) $recipe->price_per_portion,
    'time' => (int) $recipe->time,
    'kcal' => (int) $recipe->kcal,
]);

// =======================
// Planner (Plan speichern)
// =======================
Route::post('/planner/run', function (Request $r) {
    $budget  = (float) $r->input('budget', 100);
    $recipes = Recipe::inRandomOrder()->take(10)->get(['id','title','price_per_portion','time','kcal']);
    $total   = (float) $recipes->sum('price_per_portion');
    $saving  = $budget > $total ? round($budget - $total, 2) : 0.0;

    return DB::transaction(function () use ($budget, $total, $saving, $recipes) {
        $plan = Plan::create([
            'budget' => $budget,
            'total'  => round($total, 2),
            'saving' => $saving,
        ]);

        foreach ($recipes as $i => $rcp) {
            PlanMeal::create([
                'plan_id'   => $plan->id,
                'recipe_id' => $rcp->id,
                'position'  => $i + 1,
            ]);
        }

        $plan->load(['meals' => fn($q) => $q->orderBy('position'), 'meals.recipe']);

        return [
            'id'     => $plan->id,
            'budget' => (float) $plan->budget,
            'total'  => (float) $plan->total,
            'saving' => (float) $plan->saving,
            'meals'  => $plan->meals->map(fn($m) => [
                'pm_id'  => $m->id,
                'id'     => $m->recipe->id,
                'title'  => $m->recipe->title,
                'price_per_portion' => (float) $m->recipe->price_per_portion,
                'time'   => (int) $m->recipe->time,
                'kcal'   => (int) $m->recipe->kcal,
            ])->values(),
        ];
    });
});

// Plan lesen
Route::get('/plans/{plan}', function (Plan $plan) {
    $plan->load(['meals' => fn($q)=>$q->orderBy('position'), 'meals.recipe']);

    return [
        'id'     => $plan->id,
        'budget' => (float) $plan->budget,
        'total'  => (float) $plan->total,
        'saving' => (float) $plan->saving,
        'meals'  => $plan->meals->map(fn($m) => [
            'pm_id'  => $m->id,
            'id'     => $m->recipe->id,
            'title'  => $m->recipe->title,
            'price_per_portion' => (float) $m->recipe->price_per_portion,
            'time'   => (int) $m->recipe->time,
            'kcal'   => (int) $m->recipe->kcal,
        ])->values(),
    ];
});

// Meal tauschen innerhalb eines Plans
Route::post('/plans/{plan}/swap', function (Request $r, Plan $plan) {
    $pmId = (int) $r->input('plan_meal_id');
    abort_if(! $pmId, 422, 'plan_meal_id required');

    $pm = PlanMeal::where('plan_id', $plan->id)
                  ->where('id', $pmId)
                  ->firstOrFail();

    $used = PlanMeal::where('plan_id', $plan->id)->pluck('recipe_id')->all();
    $new  = Recipe::whereNotIn('id', $used)->inRandomOrder()->first()
         ?? Recipe::inRandomOrder()->first();

    $pm->update(['recipe_id' => $new->id]);

    $sum = (float) Recipe::whereIn('id',
        PlanMeal::where('plan_id',$plan->id)->pluck('recipe_id')
    )->sum('price_per_portion');

    $plan->update([
        'total'  => round($sum, 2),
        'saving' => max(0, round($plan->budget - $sum, 2)),
    ]);

    $plan->load(['meals' => fn($q)=>$q->orderBy('position'), 'meals.recipe']);

    return [
        'id'     => $plan->id,
        'budget' => (float) $plan->budget,
        'total'  => (float) $plan->total,
        'saving' => (float) $plan->saving,
        'meals'  => $plan->meals->map(fn($m) => [
            'pm_id'  => $m->id,
            'id'     => $m->recipe->id,
            'title'  => $m->recipe->title,
            'price_per_portion' => (float) $m->recipe->price_per_portion,
            'time'   => (int) $m->recipe->time,
            'kcal'   => (int) $m->recipe->kcal,
        ])->values(),
    ];
});

// =======================
// Einkaufsliste
// =======================
Route::get('/shopping-lists', function () {
    $list = ShoppingList::with(['items' => fn($q) => $q->orderBy('position')])->firstOrFail();
    $sum = $list->items->reduce(fn($c,$i) => $c + ($i->qty * $i->price), 0);

    return [
        'strategy' => $list->strategy,
        'sum'      => round($sum, 2),
        'items'    => $list->items->map(fn($i) => [
            'id'      => $i->id,
            'name'    => $i->name,
            'qty'     => (int) $i->qty,
            'price'   => (float) $i->price,
            'store'   => $i->store,
            'checked' => (bool) $i->checked,
        ])->values(),
    ];
});

Route::put('/shopping-lists/{list}/items/{item}', function (Request $r, $list, ShoppingListItem $item) {
    abort_if((int)$item->shopping_list_id !== (int)$list, 404);
    $item->checked = $r->boolean('checked');
    $item->save();

    return ['ok'=>true, 'id'=>$item->id, 'checked'=>$item->checked];
});

// =======================
// Produkte & Preise
// =======================

// Suche: GET /api/products/search?q=spa&limit=20
Route::get('/products/search', function (Request $r, PriceService $ps) {
    $q = trim((string)$r->query('q',''));
    $limit = min(50, (int)$r->query('limit', 20));

    $products = Product::when($q !== '', fn($qq)=>$qq->where('name','like','%'.$q.'%'))
                ->orderBy('name')->limit($limit)->get();

    return $products->map(function($p) use ($ps){
        $best = $ps->bestPrice($p->id);
        return [
          'id'         => $p->id,
          'name'       => $p->name,
          'unit'       => $p->unit,
          'size'       => $p->size,
          'best_price' => $best ? (float)$best['price'] : null,
          'best_store' => $best['store'] ?? null,
          'captured_at'=> $best['captured_at'] ?? null,
        ];
    });
});

// Historie: GET /api/products/{id}/prices?days=30
Route::get('/products/{product}/prices', function (Product $product, Request $r) {
    $days = (int)$r->query('days', 30);
    $from = now()->subDays($days);

    $rows = PriceSnapshot::select('price','captured_at','store_product_id')
            ->whereIn('store_product_id', StoreProduct::where('product_id',$product->id)->pluck('id'))
            ->where('captured_at','>=',$from)
            ->orderBy('captured_at','desc')
            ->get();

    return ['product'=>$product->only('id','name'),'prices'=>$rows];
});


Route::post('/matching/resolve', function (Request $r, MatchingService $ms) {
    $res = $ms->resolve(
        (string)$r->input('ingredient',''),
        $r->input('store_id'),         // optional
        $r->input('region')            // optional
    );

    if (!$res) {
        return response(['ok'=>false,'error'=>'no_match'], 404);
    }
    return ['ok'=>true, 'match'=>$res];
});


// =======================
// Preis refresh
// =======================

Route::post('/prices/refresh', function () {
    $exit = Artisan::call('app:prices-refresh');
    return ['ok' => $exit === 0, 'output' => Artisan::output()];
})->middleware('apikey');



