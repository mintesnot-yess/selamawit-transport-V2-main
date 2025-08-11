<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('action', 255);
            $table->timestamp('timestamp')->useCurrent();
            $table->string('ip_address', 45)->nullable();
            $table->date('created_at');
            $table->date('updated_at');



        });
    }

    public function down()
    {
        Schema::dropIfExists('logs');
    }
};