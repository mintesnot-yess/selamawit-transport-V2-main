<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = ["user_id", "action", "ip_address"];

    protected $casts = [
        "timestamp" => "datetime",
    ];

    // Relationship with user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Automatically set timestamp on creation
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->timestamp = now();
        });
    }
}
