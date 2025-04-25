<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProformaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'proforma_id',
        'name',
        'price',
        'quantity'
    ];

    public function proforma()
    {
        return $this->belongsTo(Proforma::class);
    }
}