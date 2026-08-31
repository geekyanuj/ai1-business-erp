<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionBatchesTable extends Migration
{
    public function up()
    {
        Schema::create('production_batches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->index();
            $table->string('batch_no', 100)->index();
            $table->string('lot_no', 100)->nullable()->index();
            $table->integer('quantity_produced')->default(0);
            $table->enum('status',['draft', 'in_progress', 'completed', 'cancelled'])->default('draft ');
            $table->date('production_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->unsignedBigInteger('operator_id')->nullable()->index();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            // If operator (user) is deleted, set to null
            $table->foreign('operator_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('production_batches');
    }
}
