<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        "expense_type_id",
        "vehicle_id",
        "employee_id",
        "order_id",
        "from_account",
        "to_account",
        "to_bank",
        "from_bank",
        "amount",
        "paid_date",
        "remark",
        "payment_type",
        "file",
        "created_by",
        "updated_by",
    ];

    protected $casts = [
        "paid_date" => "date",
        "amount" => "decimal:2",
    ];
    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function fromBank()
    {
        return $this->belongsTo(Bank::class, "from_bank");
    }

    public function toBank()
    {
        return $this->belongsTo(Bank::class, "to_bank");
    }

    // Optional: Add a generic bank() relationship if needed
    public function bank()
    {
        // Adjust logic as needed; here, just returns fromBank by default
        return $this->fromBank();
    }

    public function fromAccount()
    {
        return $this->belongsTo(BankAccount::class, "from_account");
    }

    public function bankAccount()
    {
        // Adjust the foreign key if needed
        return $this->belongsTo(BankAccount::class, "from_account");
    }
}
