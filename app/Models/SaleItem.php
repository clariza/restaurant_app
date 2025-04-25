<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'name',
        'quantity',
        'price',
        'total'
    ];

    // Relación con la venta
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}