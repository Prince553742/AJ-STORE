<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailySale extends Model
{
    protected $fillable = ['sale_date', 'total_amount'];

    protected $casts = [
        'sale_date' => 'date',   // <-- this converts the column to a Carbon instance
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}