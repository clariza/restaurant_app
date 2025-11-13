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
        'profit_margin',
        'expiry_date',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    /**
     * Relación con Purchase
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Relación con MenuItem (producto)
     * ESTA ES LA RELACIÓN QUE FALTABA
     */
    public function item()
    {
        return $this->belongsTo(MenuItem::class, 'product_id');
    }

    /**
     * Alias de la relación item para mayor claridad
     */
    public function product()
    {
        return $this->belongsTo(MenuItem::class, 'product_id');
    }

    /**
     * Relación con MenuItem a través de product_id
     * Esta es una alternativa si prefieres usar "menuItem"
     */
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'product_id');
    }

    /**
     * Verificar si el stock está próximo a vencer
     */
    public function isNearExpiry($days = 30)
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->diffInDays(now()) <= $days;
    }

    /**
     * Verificar si el stock está vencido
     */
    public function isExpired()
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->isPast();
    }

    /**
     * Calcular el costo unitario después del descuento
     */
    public function getUnitCostAfterDiscountAttribute()
    {
        return $this->unit_cost * (1 - ($this->discount / 100));
    }

    /**
     * Calcular la ganancia total
     */
    public function getTotalProfitAttribute()
    {
        $costAfterDiscount = $this->getUnitCostAfterDiscountAttribute();
        return ($this->selling_price - $costAfterDiscount) * $this->quantity;
    }
}
