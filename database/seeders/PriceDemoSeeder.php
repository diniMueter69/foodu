<?php namespace Database\Seeders; 

use Illuminate\Database\Seeder;
use App\Models\{Store,Product,StoreProduct,PriceSnapshot};

class PriceDemoSeeder extends Seeder {
  public function run(): void {
    $denner = Store::firstOrCreate(['code'=>'denner'],['name'=>'Denner']);
    $lidl   = Store::firstOrCreate(['code'=>'lidl'],  ['name'=>'Lidl']);

    $spaghetti = Product::firstOrCreate(['name'=>'Spaghetti'],['unit'=>'g','size'=>500]);
    $knobi     = Product::firstOrCreate(['name'=>'Knoblauch'],['unit'=>'stk','size'=>1]);

    $sp_den = StoreProduct::firstOrCreate(['store_id'=>$denner->id,'product_id'=>$spaghetti->id],['pack_size'=>500,'pack_unit'=>'g']);
    $sp_lidl= StoreProduct::firstOrCreate(['store_id'=>$lidl->id,  'product_id'=>$spaghetti->id],['pack_size'=>500,'pack_unit'=>'g']);
    $kb_lidl= StoreProduct::firstOrCreate(['store_id'=>$lidl->id,  'product_id'=>$knobi->id],['pack_size'=>1,'pack_unit'=>'stk']);

    foreach ([['sp'=>$sp_den,'p'=>1.20],['sp'=>$sp_lidl,'p'=>1.15],['sp'=>$kb_lidl,'p'=>0.50]] as $row) {
      PriceSnapshot::create([
        'store_product_id'=>$row['sp']->id,'region'=>null,'price'=>$row['p'],'captured_at'=>now(),
      ]);
    }
  }
}