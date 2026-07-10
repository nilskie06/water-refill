<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BottleBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'bottles_out',
        'bottles_returned',
        'balance',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public static function updateForCustomer($customerId, $bottleOut = 0, $bottleIn = 0)
    {
        $balance = self::firstOrCreate(['customer_id' => $customerId]);

        $balance->bottles_out += $bottleOut;
        $balance->bottles_returned += $bottleIn;
        $balance->balance = $balance->bottles_out - $balance->bottles_returned;
        $balance->save();

        return $balance;
    }
}
