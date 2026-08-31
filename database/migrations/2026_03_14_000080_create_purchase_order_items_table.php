<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('purchase_order_id')->index();

            // Optional product reference
            $table->unsignedBigInteger('product_id')->nullable()->index();

            $table->string('product_name');
            $table->string('product_description')->nullable();

            
            $table->integer('quantity')->default(0);
            $table->decimal('unit_price', 12, 2)->default(0.00);
            $table->string('uom')->nullable();
            $table->decimal('total', 12, 2)->default(0.00);
            
            $table->string('hsn_code', 20)->nullable();
            $table->decimal('tax_rate', 5, 2)->default(0);
            
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_with_tax', 12, 2)->default(0);
            
            $table->timestamps();

            // Relations
            $table->foreign('purchase_order_id')
                  ->references('id')
                  ->on('purchase_orders')
                  ->onDelete('cascade');

            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->nullOnDelete(); // sets product_id to null if product is deleted
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_order_items');
    }
}
