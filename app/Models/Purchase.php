<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'supplier_id',
        'product',
        'price',
        'quantity',
        'purchase_date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}