<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'cost',
        'image',
        'category_id',
        'stock',
        'is_available',
        'preparation_time',
        'min_stock',
        'stock_type',
        'stock_unit',
        'manage_inventory'
    ];

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
    public function updateStock($quantity, $movementType, $notes = null, $userId = null)
    {
        $oldStock = $this->stock;

        $this->stock = $movementType === 'addition'
            ? $this->stock + $quantity
            : $this->stock - $quantity;

        $this->save();
        $currentUserId = null;
        if ($userId) {
            // Si se proporciona un userId específico
            $currentUserId = $userId;
        } elseif (function_exists('auth') && auth() && auth()->check()) {
            // Si auth() está disponible y hay usuario autenticado
            $currentUserId = auth()->id();
        }

        // Usar el userId proporcionado o el usuario autenticado
        // $currentUserId = $userId ?? (auth()->check() ? auth()->id() : null);
        // Registrar movimiento
        return $this->inventoryMovements()->create([
            'user_id' => $currentUserId,
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
    public function branchStocks()
    {
        return $this->hasMany(BranchMenuItemStock::class);
    }
    public function getStockForBranch(int $branchId): float
    {
        $branchStock = $this->branchStocks()
            ->where('branch_id', $branchId)
            ->first();

        return $branchStock ? $branchStock->stock : $this->stock; // fallback al stock global
    }
    // Actualizar stock para una sucursal específica
    public function updateStockForBranch(
        int $branchId,
        float $quantity,
        string $movementType,
        ?string $notes = null,
        ?int $userId = null
    ): InventoryMovement {
        $branchStock = BranchMenuItemStock::firstOrCreate(
            ['branch_id' => $branchId, 'menu_item_id' => $this->id],
            ['stock' => $this->stock, 'min_stock' => $this->min_stock]
        );

        $oldStock = $branchStock->stock;

        $branchStock->stock = $movementType === 'addition'
            ? $branchStock->stock + $quantity
            : $branchStock->stock - $quantity;

        $branchStock->save();


        $currentUserId = $userId ?? Auth::id();

        return $this->inventoryMovements()->create([
            'user_id'       => $currentUserId,
            'branch_id'     => $branchId,
            'movement_type' => $movementType,
            'quantity'      => $quantity,
            'old_stock'     => $oldStock,
            'new_stock'     => $branchStock->stock,
            'notes'         => $notes,
        ]);
    }
}
