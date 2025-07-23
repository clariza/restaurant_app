<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 
    'price', 'image', 
    'category_id','stock'];

    // Relación con la categoría
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function stocks()
    {
        return $this->hasMany(Stock::class, 'product_id');
    }
}
