<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = ['daily_sale_id', 'item_id', 'item_name', 'price', 'quantity', 'total'];
}