<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('expense_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 244);
            $table->enum('category', ['Private', 'Own', 'General', 'Vehicle', 'Employee']);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->constrained('users')->cascadeOnDelete();
            $table->date('created_at');
            $table->date('updated_at');


        });
    }

    public function down()
    {
        Schema::dropIfExists('expense_types');
    }
};