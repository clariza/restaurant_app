<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'last_name',
        'phone',
        'email',
        'document_type',
        'document_number',
        'address',
        'city',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Accessor para nombre completo
    public function getFullNameAttribute()
    {
        return "{$this->name} {$this->last_name}";
    }

    // Scope para clientes activos
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
