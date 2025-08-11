<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        "first_name",
        "last_name",
        "email",
        "phone",
        "id_file",
        "type",
        "created_by",
        "updated_by",
    ];

    protected $casts = [
        "hire_date" => "date",
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function updater()
    {
        return $this->belongsTo(User::class, "updated_by");
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
