<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'contact', 'address'];

    public function creditSales()
    {
        return $this->hasMany(CreditSale::class);
    }

    public function getTotalPendingAttribute()
    {
        return $this->creditSales()->where('status', 'pending')->sum('total');
    }
}