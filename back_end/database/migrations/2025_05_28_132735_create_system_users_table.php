<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('system_users', function (Blueprint $table) {
            $table->id();
            $table->string('user_name', 255);
            $table->string('email', 255);
            $table->string('password', 255);

            $table->foreignId('created_by')->constrained('system_users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('updated_by')->constrained('system_users')->cascadeOnDelete()->cascadeOnUpdate();


            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('system_users');
    }
};