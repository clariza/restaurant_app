<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proforma extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_phone',
        'notes',
        'order_type',
        'subtotal',
        'tax',
        'total',
        'status',
        'user_id',
        'branch_id',
        'is_converted',
        'converted_to_order',
        'converted_order_id',
        'converted_at'
    ];
    protected $casts = [
        'converted_to_order' => 'boolean',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'is_converted' => 'boolean',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'converted_at' => 'datetime',
    ];
    public function items()
    {
        return $this->hasMany(ProformaItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function order()
    {
        return $this->belongsTo(Sale::class, 'converted_order_id');
    }

    // Método para verificar si puede ser convertida
    // En App\Models\Proforma.php

    public function canBeConverted()
    {
        if ($this->isConverted()) {
            return false;
        }
        // ✅ AGREGAR esta validación primero
        if ($this->converted_to_order) {
            return false;
        }
        $openPettyCash = \App\Models\PettyCash::where('status', 'open')->exists();
        if (!$openPettyCash) {
            return false;
        }

        // Validación de status cancelado
        if ($this->status === 'cancelled') {
            return false;
        }

        // Validar stock disponible
        foreach ($this->items as $item) {
            $menuItem = $item->menuItem;

            if ($menuItem && $menuItem->manage_inventory) {
                if ($menuItem->stock < $item->quantity) {
                    return false;
                }
            }
        }

        return true;
    }
    public function markAsConverted(int $orderId): void
    {
        $this->update([
            'is_converted' => true,
            'converted_order_id' => $orderId,
            'converted_at' => now(),
        ]);
    }
    /**
     * Desmarca la proforma como convertida (por ejemplo, si se elimina la orden)
     */
    public function unmarkAsConverted(): void
    {
        $this->update([
            'is_converted' => false,
            'converted_order_id' => null,
            'converted_at' => null,
        ]);
    }

    // En App\Models\Proforma.php

    public function isConverted()
    {
        return $this->converted_to_order == true;
    }
    public function convertedOrder()
    {
        return $this->belongsTo(Sale::class, 'converted_order_id');
    }
    public function getConversionStatusAttribute(): string
    {
        if ($this->isConverted()) {
            return 'Convertida';
        }

        if ($this->status === 'cancelled') {
            return 'Cancelada';
        }

        if ($this->canBeConverted()) {
            return 'Pendiente';
        }

        return 'No disponible';
    }
    public function scopeNotConverted($query)
    {
        return $query->where('converted_to_order', false)
            ->orWhereNull('converted_to_order');
    }

    public function scopeConverted($query)
    {
        return $query->where('converted_to_order', true);
    }
    public function scopeCanBeConverted($query)
    {
        return $query->where('is_converted', false);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
