<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanMeal extends Model
{
    protected $fillable = ['plan_id','recipe_id','position'];
    public function plan()   { return $this->belongsTo(Plan::class); }
    public function recipe() { return $this->belongsTo(\App\Models\Recipe::class); }
}
