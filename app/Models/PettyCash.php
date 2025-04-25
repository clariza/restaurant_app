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
        'total_sales_cash', // Nuevo campo
        'total_sales_qr',   // Nuevo campo
        'total_sales_card', // Nuevo campo
        'total_expenses',   // Nuevo campo
        'total_general',
        'closed_at',        // Nuevo campo
    ];
    protected $casts = [
        'date' => 'date',
        'closed_at' => 'datetime',
    ];
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
