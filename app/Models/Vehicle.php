<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = ['plate_number', 'description', 'capacity', 'status'];

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
