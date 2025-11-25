<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nit',
        'address',
        'contact',
        'phone',
        'address',
        'is_active'
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
    protected $casts = [
        'is_active' => 'boolean'
    ];
    /**
     * Obtener el total de compras realizadas a este proveedor
     */
    public function getTotalPurchasesAttribute()
    {
        return $this->purchases()->sum('total_amount');
    }

    /**
     * Scope para proveedores activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
