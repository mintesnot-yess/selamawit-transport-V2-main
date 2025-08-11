<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number', 50);
            $table->string('vehicle_name', 255);
            $table->string('owner_name', 255);
            $table->string('owner_phone', 50)->nullable();
            $table->enum('owner_type', ['OWNED', 'PRIVATE']);
            $table->string('libre', 100)->nullable();

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
};