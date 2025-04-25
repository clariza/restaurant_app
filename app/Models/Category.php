<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'icon'];

    // Relación con los elementos del menú
    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
