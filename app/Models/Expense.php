<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'description',
        'amount',
        'date',
        'category',
        'subcategory',
        'petty_cash_id',
    ];
    // Relación con la caja chica
    public function pettyCash()
    {
        return $this->belongsTo(PettyCash::class);
    }
}
