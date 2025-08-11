<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create("incomes", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("order_id")
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->decimal("amount", 12, 2);
            $table->date("received_date");
            $table->foreignId('bank_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('account_number')->nullable();
            $table->string('payment_type')->nullable();
            $table->string("attachment");
            $table->text(column: "remark")->nullable();

            $table
                ->foreignId("created_by")
                ->constrained("users")
                ->cascadeOnDelete();
            $table
                ->foreignId("updated_by")
                ->constrained("users")
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("income");
    }
};
