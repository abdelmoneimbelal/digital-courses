<?php

namespace App\Models;

use Laravel\Cashier\Cashier;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $guarded = [];

    protected $with = ['courses'];

    public function scopeSession()
    {
        return $this->where('session_id', session()->getId());
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }

    public function total()
    {
        return Cashier::formatAmount($this->courses->sum('price'), env('CASHIER_CURRENCY'));
    }
}
