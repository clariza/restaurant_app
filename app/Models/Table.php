<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;
    protected $table = 'tables'; 
    
    protected $fillable = [
        'number', 
        'state', 
    ];
     protected $attributes = [
        'state' => 'Disponible',
    ];

    protected $casts = [
        'number' => 'integer', 
    ];
     public static $validStates = [
        'Disponible',
        'Ocupada',
        'Reservada',
        'No Disponible'
    ];
    
    public function scopeAvailable($query)
    {
        return $query->where('state', 'Disponible');
    }
    
    public function isAvailable()
    {
        return $this->state === 'Disponible';
    }
    public function isUnavailable()
    {
        return $this->state === 'No Disponible';
    }
    public function isOccupied()
    {
        return $this->state === 'Ocupada';
    }
    
    public function isReserved()
    {
        return $this->state === 'Reservada';
    }
    // Nuevo método para cambio masivo
    public static function bulkUpdateState($newState)
    {
        if (!in_array($newState, self::$validStates)) {
            throw new \InvalidArgumentException("Estado inválido: {$newState}");
        }
        
        return self::query()->update(['state' => $newState]);
    }
    
    // Método para obtener conteos por estado
    public static function getStateCounts()
    {
        return self::selectRaw('state, COUNT(*) as count')
                  ->groupBy('state')
                  ->pluck('count', 'state')
                  ->toArray();
    }
}
