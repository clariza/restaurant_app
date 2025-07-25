<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'supplier_id',
        'reference_number',
        'purchase_date',
        'total_amount',
        'status',
    ];


    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}