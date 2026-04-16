<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'phone',
        'order_type',
        'table_number',
        'subtotal',
        'discount',
        'service_charge',
        'delivery_service',
        'pickup_notes',
        'tax',
        'subtotal',
        'total',
        'transaction_number',
        'petty_cash_id',
        'branch_id',
        'proforma_id',
        'payment_method',
        'order_notes',
        'daily_order_number',
        'order_date',
        'transaction_number_ref',
    ];
    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'order_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public static function generateOrderNumber()
    {
        $today = now()->toDateString();
        $lastSale = self::whereDate('order_date', $today)
            ->whereNotNull('daily_order_number')
            ->orderBy('daily_order_number', 'desc')
            ->first();
        if ($lastSale && $lastSale->daily_order_number) {
            if (preg_match('/PED-(\d+)/', $lastSale->daily_order_number, $matches)) {
                $lastNumber = (int) $matches[1];
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }
        } else {
            $nextNumber = 1;
        }
        return 'PED-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    public static function generateTransactionNumber()
    {
        // Buscar el último registro con transaction_number en formato ORD-NNNNN
        $last = self::whereNotNull('transaction_number')
            ->where('transaction_number', 'like', 'ORD-%')
            ->orderByRaw("CAST(SUBSTRING(transaction_number, 5) AS UNSIGNED) DESC")
            ->lockForUpdate()  // evita condición de carrera en inserciones concurrentes
            ->first();

        if ($last && preg_match('/ORD-(\d+)/', $last->transaction_number, $matches)) {
            $next = (int) $matches[1] + 1;
        } else {
            $next = 1;
        }

        return 'ORD-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function pettyCash()
    {
        return $this->belongsTo(PettyCash::class, 'petty_cash_id');
    }
    public function proforma()
    {
        return $this->belongsTo(Proforma::class, 'proforma_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
    public function isFromProforma(): bool
    {
        return !is_null($this->proforma_id);
    }
    public function getSourceProforma()
    {
        return $this->proforma;
    }
    public function scopeForBranch($query, $branchId)
    {
        if ($branchId) {
            return $query->where('branch_id', $branchId);
        }
        return $query;
    }
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('order_date', $date);
    }
    public function scopeForDateRange($query, $from, $to)
    {
        return $query->whereBetween('order_date', [$from, $to]);
    }
    public function scopeToday($query)
    {
        return $query->whereDate('order_date', Carbon::today());
    }
    public function getFormattedTotalAttribute()
    {
        return number_format($this->total, 2);
    }
    public function getBranchNameAttribute()
    {
        return $this->branch ? $this->branch->name : 'Sin sucursal';
    }
}
