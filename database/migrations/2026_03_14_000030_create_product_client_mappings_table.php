<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
            Schema::create('product_client_mappings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('product_id')->index(); 
                $table->unsignedBigInteger('client_id')->index(); 
                $table->string('client_part_no', 255)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                // Foreign key to products
                $table->foreign('product_id')
                    ->references('id')
                    ->on('products')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                // Foreign key to clients
                $table->foreign('client_id')
                    ->references('id')
                    ->on('clients')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                 $table->unique(['product_id', 'client_id', 'client_part_no'], 'pcm_product_client_part_unique');
            });
    }

    public function down()
    {
        Schema::dropIfExists('product_client_mappings');
    }
};
