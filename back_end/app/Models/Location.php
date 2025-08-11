<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ["location_name", "created_by", "updated_by"];

    // Relationship with orders as loading place
    public function loadingOrders()
    {
        return $this->hasMany(Order::class, "loading_place");
    }

    // Relationship with orders as destination
    public function destinationOrders()
    {
        return $this->hasMany(Order::class, "destination");
    }

    // Relationship with creator
    public function creator()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    // Relationship with updater
    public function updater()
    {
        return $this->belongsTo(User::class, "updated_by");
    }
}
