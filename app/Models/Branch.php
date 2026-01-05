<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'email',
        'city',
        'state',
        'is_active',
        'is_main',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_main' => 'boolean',
    ];

    /**
     * Relación con usuarios
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relación con órdenes
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Relación con cajas chicas
     */
    public function pettyCashes(): HasMany
    {
        return $this->hasMany(PettyCash::class);
    }

    /**
     * Scope para sucursales activas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para sucursal principal
     */
    public function scopeMain($query)
    {
        return $query->where('is_main', true);
    }

    /**
     * Obtener sucursal principal
     */
    public static function getMainBranch()
    {
        return static::main()->first();
    }
}
