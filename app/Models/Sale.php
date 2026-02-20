<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'phone',
        'order_type',
        'table_number',
        'subtotal',
        'discount',
        'service_charge',
        'delivery_service',
        'pickup_notes',
        'tax',
        'subtotal',
        'total',
        'transaction_number',
        'petty_cash_id',
        'branch_id',
        'proforma_id',
        'payment_method',
        'order_notes',
        'daily_order_number',
        'order_date',
        'transaction_number_ref',
    ];


    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'order_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public static function generateOrderNumber()
    {
        $today = now()->toDateString();

        // Obtener el último número de pedido del día
        $lastSale = self::whereDate('order_date', $today)
            ->whereNotNull('daily_order_number')
            ->orderBy('daily_order_number', 'desc')
            ->first();

        if ($lastSale && $lastSale->daily_order_number) {
            // Extraer solo los dígitos del formato "PED-00001"
            if (preg_match('/PED-(\d+)/', $lastSale->daily_order_number, $matches)) {
                $lastNumber = (int) $matches[1];
                $nextNumber = $lastNumber + 1;
            } else {
                // Si no tiene el formato esperado, empezar en 1
                $nextNumber = 1;
            }
        } else {
            // Si no hay pedidos hoy, empezar en 1
            $nextNumber = 1;
        }

        // Formatear con prefijo y ceros a la izquierda (5 dígitos)
        return 'PED-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pettyCash()
    {
        return $this->belongsTo(PettyCash::class, 'petty_cash_id');
    }
    public function proforma()
    {
        return $this->belongsTo(Proforma::class, 'proforma_id');
    }
    /**
     * Relación con la sucursal
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    /**
     * Relación con los items de la venta
     */
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
    public function isFromProforma(): bool
    {
        return !is_null($this->proforma_id);
    }
    /**
     * Obtiene la proforma origen si existe
     */
    public function getSourceProforma()
    {
        return $this->proforma;
    }

    public static function generateTransactionNumber()
    {
        do {
            $number = 'ORD-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('transaction_number', $number)->exists());

        return $number;
    }
    /**
     * Scope para filtrar por sucursal
     */
    public function scopeForBranch($query, $branchId)
    {
        if ($branchId) {
            return $query->where('branch_id', $branchId);
        }
        return $query;
    }
    /**
     * Scope para filtrar por fecha
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('order_date', $date);
    }
    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopeForDateRange($query, $from, $to)
    {
        return $query->whereBetween('order_date', [$from, $to]);
    }
    /**
     * Scope para ventas del día actual
     */
    public function scopeToday($query)
    {
        return $query->whereDate('order_date', Carbon::today());
    }

    /**
     * Accessor para formatear el total
     */
    public function getFormattedTotalAttribute()
    {
        return number_format($this->total, 2);
    }
    /**
     * Accessor para el nombre de la sucursal
     */
    public function getBranchNameAttribute()
    {
        return $this->branch ? $this->branch->name : 'Sin sucursal';
    }
}
