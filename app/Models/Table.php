<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;
    protected $table = 'tables'; 

    protected $fillable = [
        'number', // Número de la mesa
        'state', // Estado de la mesa
    ];

    protected $casts = [
        'number' => 'integer', // Asegura que el número sea un entero
        'state' => 'string',  // Asegura que el estado sea una cadena
    ];
}
