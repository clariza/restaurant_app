<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'product_id',
        'purchase_id',
        'quantity',
        'unit_cost',
        'discount',
        'total_cost',
        'selling_price',
        'expiry_date',
    ];

    public function product()
    {
        return $this->belongsTo(MenuItem::class, 'product_id');
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
