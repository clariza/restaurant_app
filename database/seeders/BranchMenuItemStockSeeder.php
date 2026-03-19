<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MenuItem;
use App\Models\Branch;

class BranchMenuItemStockSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::where('is_active', true)->get();
        $items    = MenuItem::where('manage_inventory', true)->get();
        $mainBranch = Branch::where('is_main', true)->first();

        foreach ($items as $item) {
            foreach ($branches as $branch) {
                // Evitar duplicados si ya existe el registro
                $exists = DB::table('branch_menu_item_stock')
                    ->where('branch_id', $branch->id)
                    ->where('menu_item_id', $item->id)
                    ->exists();

                if (!$exists) {
                    DB::table('branch_menu_item_stock')->insert([
                        'branch_id'    => $branch->id,
                        'menu_item_id' => $item->id,
                        // El stock actual del producto solo va a la sucursal principal
                        // Las demás sucursales arrancan en 0
                        'stock'     => ($mainBranch && $branch->id === $mainBranch->id)
                                        ? $item->stock
                                        : 0,
                        'min_stock'  => $item->min_stock,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info('Stock por sucursal inicializado correctamente.');
    }
}