<?php
// app/Models/BranchMenuItemStock.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchMenuItemStock extends Model
{
    protected $table = 'branch_menu_item_stock';

    protected $fillable = [
        'branch_id',
        'menu_item_id',
        'stock',
        'min_stock',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
