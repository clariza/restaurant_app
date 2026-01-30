<?php

namespace App\Models;

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

    // Relación con los ítems de venta
    public function items()
    {
        return $this->hasMany(SaleItem::class);
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
}
