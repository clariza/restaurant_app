<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 
    'price', 'image', 
    'category_id','stock','min_stock',
        'stock_type','stock_unit'];

    // Relación con la categoría
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    // Relación con los movimientos de inventario
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }
    public function updateStock($quantity, $movementType, $notes = null)
    {
        $oldStock = $this->stock;
        
        $this->stock = $movementType === 'addition' 
            ? $this->stock + $quantity
            : $this->stock - $quantity;
            
        $this->save();

        // Registrar movimiento
        return $this->inventoryMovements()->create([
            'user_id' => auth()->id(),
            'movement_type' => $movementType,
            'quantity' => $quantity,
            'old_stock' => $oldStock,
            'new_stock' => $this->stock,
            'notes' => $notes
        ]);
    }
      // Método para verificar bajo stock
    public function isLowStock()
    {
        return $this->stock < $this->min_stock;
    }
    
    public function stocks()
    {
        return $this->hasMany(Stock::class, 'product_id');
    }
    public function isOutOfStock()
    {
        return $this->stock <= 0;
    }
}
