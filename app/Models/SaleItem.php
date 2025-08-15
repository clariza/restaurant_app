<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'menu_item_id',
        'name',
        'quantity',
        'price',
        'total'
    ];
     /**
     * Relación con MenuItem (Producto)
     */
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    // Relación con la venta
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}