<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['budget','total','saving'];
    public function meals() { return $this->hasMany(PlanMeal::class); }
}
