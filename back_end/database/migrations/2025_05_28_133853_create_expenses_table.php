<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();

            $table->foreignId('expense_type_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('vehicle_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('employee_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('order_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreignId('from_account')->constrained('bank_accounts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('to_account')->nullable();
            $table->foreignId('to_bank')->constrained('banks')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('from_bank')->constrained('banks')->cascadeOnDelete()->cascadeOnUpdate();

            $table->decimal('amount', 12, 2);
            $table->date('paid_date');
            $table->text('remark')->nullable();
            $table->text('payment_type')->nullable();
            $table->string('file', 255)->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();



        });
    }

    public function down()
    {
        Schema::dropIfExists('expenses');
    }
};