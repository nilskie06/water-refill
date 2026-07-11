<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_no', 'order_id', 'customer_id', 'driver_id', 'vehicle_id',
        'delivery_date', 'delivery_time', 'address', 'contact_number',
        'quantity', 'delivery_type', 'status', 'route', 'remarks',
    ];

    protected function casts(): array
    {
        return [
            'delivery_date' => 'date',
            'delivery_time' => 'datetime:H:i',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public static function generateDeliveryNo(): string
    {
        $date = now()->format('Ymd');
        $last = self::where('delivery_no', 'like', "DLT-{$date}-%")->orderByDesc('delivery_no')->first();
        $seq = $last ? intval(substr($last->delivery_no, -4)) + 1 : 1;
        return "DLT-{$date}-" . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('delivery_date', $date);
    }

    public function scopeForDateRange($query, $from, $to)
    {
        return $query->whereBetween('delivery_date', [$from, $to]);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'scheduled' => 'badge-pending',
            'assigned' => 'badge-delivered',
            'out_for_delivery' => 'badge-completed',
            'delivered' => 'badge-completed',
            'failed' => 'badge-cancelled',
            'cancelled' => 'badge-cancelled',
            default => '',
        };
    }

    public function getDeliveryTypeLabelAttribute(): string
    {
        return match($this->delivery_type) {
            'regular' => '🚚 Regular',
            'rush' => '⚡ Rush',
            'scheduled' => '📅 Scheduled',
            'pickup' => '📦 Pickup',
            default => $this->delivery_type,
        };
    }
}
