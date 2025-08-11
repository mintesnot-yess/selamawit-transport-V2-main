<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadType extends Model
{
    use HasFactory;

    protected $fillable = ["name", "created_by", "updated_by"];

    // Relationship with orders
    public function orders()
    {
        return $this->hasMany(Order::class);
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
