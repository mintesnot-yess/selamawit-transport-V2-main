<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        "client_id",
        "employee_id",
        "vehicle_id",
        "loading_place",
        "destination",
        "load_type_id",
        "quintal",
        "given_tariff",
        "sub_tariff",
        "order_date",
        "arrival_at_loading_site",
        "loading_date",
        "current_condition",
        "payment_collected",
        "status",
        "created_by",
        "updated_by",
    ];

    protected $casts = [
        "order_date" => "datetime",
        "arrival_at_loading_site" => "date",
        "loading_date" => "date",
        "quintal" => "decimal:2",
        "given_tariff" => "decimal:2",
        "sub_tariff" => "decimal:2",
        "payment_collected" => "boolean",
    ];

    // Relationship with customer
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relationship with employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Relationship with vehicle
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    // Relationship with load type
    public function loadType()
    {
        return $this->belongsTo(LoadType::class);
    }

    // Relationship with loading location
    public function loadingLocation()
    {
        return $this->belongsTo(Location::class, "loading_place");
    }

    // Relationship with destination location
    public function destinationLocation()
    {
        return $this->belongsTo(Location::class, "destination");
    }

    // Relationship with incomes
    public function incomes()
    {
        return $this->hasMany(Income::class);
    }
    public function expense()
    {
        return $this->hasMany(Expense::class);
    }


    public function income()
    {
        return $this->hasMany(Income::class);
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
