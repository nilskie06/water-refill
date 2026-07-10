<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'order_date',
        'product',
        'quantity',
        'unit_price',
        'delivery_type',
        'bottle_in',
        'bottle_out',
        'total',
        'status',
    ];

    protected $casts = [
        'order_date' => 'date',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getAmountPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getBalanceAttribute()
    {
        return $this->total - $this->amount_paid;
    }

    public static function generateOrderNumber()
    {
        $today = now()->format('Ymd');
        $lastOrder = self::where('order_number', 'like', "ORD-{$today}-%")
            ->orderByDesc('order_number')
            ->first();

        if ($lastOrder) {
            $lastNum = intval(substr($lastOrder->order_number, -4));
            $newNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNum = '0001';
        }

        return "ORD-{$today}-{$newNum}";
    }
}
