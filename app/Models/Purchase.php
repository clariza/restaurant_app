<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'branch_id',
        'user_id',
        'reference_number',
        'purchase_date',
        'total_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Relación con Supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Relación con Stock (detalles de la compra)
     */
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Alias para la relación stocks
     */
    public function details()
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Obtener todos los productos de esta compra a través de stocks
     */
    public function products()
    {
        return $this->hasManyThrough(
            MenuItem::class,
            Stock::class,
            'purchase_id', // Foreign key en stocks
            'id', // Foreign key en menu_items
            'id', // Local key en purchases
            'product_id' // Local key en stocks
        );
    }

    /**
     * Calcular el total de items en la compra
     */
    public function getTotalItemsAttribute()
    {
        return $this->stocks->sum('quantity');
    }

    /**
     * Calcular el costo total de la compra
     */
    public function getTotalCostAttribute()
    {
        return $this->stocks->sum('total_cost');
    }

    /**
     * Verificar si la compra está pendiente
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Verificar si la compra está completada
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Verificar si la compra está cancelada
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para filtrar por fecha
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('purchase_date', $date);
    }

    /**
     * Scope para filtrar por proveedor
     */
    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }
    public function hasItemsNearExpiry($days = 30)
    {
        return $this->stocks()
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays($days))
            ->exists();
    }
}
