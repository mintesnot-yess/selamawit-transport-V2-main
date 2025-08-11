<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('loading_place')->constrained(table: 'locations')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('destination')->constrained(table: 'locations')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('load_type_id')->constrained(table: 'load_types')->cascadeOnDelete()->cascadeOnUpdate();

            $table->decimal('quintal', 10, 2)->nullable();
            $table->decimal('given_tariff', 12, 2)->nullable();
            $table->integer('sub_tariff');
            $table->timestamp('order_date')->useCurrent();
            $table->date('arrival_at_loading_site')->nullable();
            $table->date('loading_date')->nullable();
            $table->enum('current_condition', ['LOADED', 'OFFLOADED'])->nullable();
            $table->boolean('payment_collected')->default(false);
            $table->enum('status', ['PENDING', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED'])->default('PENDING');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();



        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};