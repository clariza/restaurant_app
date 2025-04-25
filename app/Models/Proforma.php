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
        'table_number',
        'subtotal',
        'tax',
        'total',
        'status',
        'user_id',
        'converted_to_order',
        'converted_order_id'
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
    public function canBeConverted()
    {
        return !$this->converted_to_order && $this->status !== 'cancelled';
    }
}