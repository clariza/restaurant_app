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
        'phone',
        'order_type',
        'table_number',
        'subtotal',
        'discount',
        'service_charge',
        'tax',
        'total',
        'transaction_number', 
        'petty_cash_id',
        'payment_method',
        'order_notes', // Agregar este campo
    ];

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
    public static function generateTransactionNumber()
    {
        do {
            $number = 'ORD-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('transaction_number', $number)->exists());
    
        return $number; 
    }
}