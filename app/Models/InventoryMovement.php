<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $fillable = [
        'menu_item_id',
        'user_id',
        'movement_type',
        'quantity',
        'old_stock',
        'new_stock',
        'notes'
    ];
    protected $casts = [
        'quantity' => 'decimal:2',
        'previous_stock' => 'decimal:2',
        'new_stock' => 'decimal:2'
    ];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
     // Accesor para el tipo de movimiento
    public function getTypeNameAttribute()
    {
        return $this->movement_type === 'addition' ? 'Ingreso' : 'Salida';
    }
}