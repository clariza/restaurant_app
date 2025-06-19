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
     protected $attributes = [
        'state' => 'Disponible',
    ];

    protected $casts = [
        'number' => 'integer', // Asegura que el número sea un entero
    ];
    public static $validStates = ['Disponible', 'Ocupada', 'Reservada'];
    
    public function scopeAvailable($query)
    {
        return $query->where('state', 'Disponible');
    }
    
    public function isAvailable()
    {
        return $this->state === 'Disponible';
    }
    
    public function isOccupied()
    {
        return $this->state === 'Ocupada';
    }
    
    public function isReserved()
    {
        return $this->state === 'Reservada';
    }
}
