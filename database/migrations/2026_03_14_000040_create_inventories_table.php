<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoriesTable extends Migration
{
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->enum('inventory_type', ['raw', 'ready'])->index();

            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->string('material_name')->nullable(); // for raw materials not in products

            $table->string('uom', 20)->default('pcs'); // pcs, kg, meter

            $table->integer('quantity_available')->default(0);
            $table->integer('quantity_reserved')->default(0);

            $table->string('location')->nullable();

            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')->on('products')
                ->nullOnDelete();

            $table->unique(['inventory_type', 'product_id', 'material_name']);
        });

    }

    public function down()
    {
        Schema::dropIfExists('inventories');
    }
}
