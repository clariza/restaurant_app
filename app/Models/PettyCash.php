<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyCash extends Model
{
    use HasFactory;

    protected $table = 'petty_cash'; 

    protected $fillable = [
        'initial_amount',
        'current_amount',
        'date',
        'notes',
        'status',
        'user_id', 
        'total_sales_cash', 
        'total_sales_qr',   
        'total_sales_card', 
        'total_expenses',   
        'total_general',
        'closed_at',        
    ];
    protected $casts = [
        'date' => 'date',
        'closed_at' => 'datetime',
    ];
     // Relación con el usuario (cajero)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con los gastos
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
    // En el modelo PettyCash
    public function sales()
    {
        return $this->hasMany(Sale::class,'petty_cash_id');
    }
}
