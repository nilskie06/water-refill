<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'contact', 'address', 'notes'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function bottleBalance()
    {
        return $this->hasOne(BottleBalance::class);
    }

    public function getTotalOrdersAttribute()
    {
        return $this->orders()->count();
    }

    public function getTotalSpentAttribute()
    {
        return $this->orders()->sum('total');
    }
}
