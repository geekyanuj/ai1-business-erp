<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('our_part_no', 100)->unique();
            $table->text('description')->nullable();
            $table->enum('category', [
                'RF Antenna',
                'RF Cable',
                'RF Cable Assembly',
                'Microwave Device',
                'IoT',
            ]);
            $table->longText('specs')->nullable();
            $table->string('hsn', 20)->nullable()->comment('HSN code of the product');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
