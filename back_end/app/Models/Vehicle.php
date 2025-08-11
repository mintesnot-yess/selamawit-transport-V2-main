<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        "plate_number",
        'vehicle_name',
        "owner_name",
        "owner_phone",
        "owner_type",
        "libre",
        "created_by",
        "updated_by",
    ];

    protected $casts = [
        "owner_type" => "string",
    ];

     public function expense()
    {
        return $this->hasMany(Expense::class);
    }
       
    public function creator()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function updater()
    {
        return $this->belongsTo(User::class, "updated_by");
    }

}
