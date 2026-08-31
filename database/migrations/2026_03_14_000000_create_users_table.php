<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id'); // BIGINT AUTO_INCREMENT PRIMARY KEY
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['Production', 'Warehouse', 'Admin', 'Sales'])->default('Sales');
            $table->timestamp('last_login')->nullable();
            $table->boolean('login_enabled')->default(true);
            $table->string('telephone')->nullable();
            $table->rememberToken(); // adds 'remember_token' column
            $table->timestamps(); // adds 'created_at' and 'updated_at'
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
