<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grn_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('grn_id')->index();
            $table->unsignedBigInteger('purchase_order_item_id')->index();
            $table->unsignedBigInteger('product_id')->nullable();
             $table->string('location')->nullable();

            $table->string('material_name')->nullable();
            $table->decimal('quantity_received', 12, 2);

            $table->timestamps();

            $table->foreign('grn_id')
                ->references('id')->on('grns')
                ->cascadeOnDelete();

            $table->foreign('product_id')
                ->references('id')->on('products')
                ->nullOnDelete();
            
             $table->foreign('purchase_order_item_id')
                  ->references('id')
                  ->on('purchase_order_items')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grn_items');
    }
};
