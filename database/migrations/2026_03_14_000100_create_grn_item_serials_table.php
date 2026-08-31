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
        Schema::create('grn_item_serials', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('grn_item_id')->index();
            $table->unsignedBigInteger('inventory_id')->nullable()->index();

            // Supplier serial
            $table->string('supplier_serial')->index();
            $table->enum('status',['in_stock','shipped'])->nullable();

            // Optional future use
            $table->string('our_serial')->nullable();

            $table->timestamps();

            $table->foreign('grn_item_id')
                ->references('id')->on('grn_items')
                ->cascadeOnDelete();

            $table->foreign('inventory_id')
                ->references('id')->on('inventories')
                ->nullOnDelete();

            $table->unique(['inventory_id', 'supplier_serial']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grn_item_serials');
    }
};
