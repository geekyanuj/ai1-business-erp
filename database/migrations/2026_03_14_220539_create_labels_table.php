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
        Schema::create('labels', function (Blueprint $table) {
            $table->id();

           $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            
            $table->string('lot_no')->index();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique('lot_no');

        });

        // Create label_items table
        Schema::create('label_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('label_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('serial_no')->index();

            $table->string('item_code')->unique();
            $table->timestamps();

             

            // performance indexes
            $table->index('label_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('label_items');
        Schema::dropIfExists('labels');
    }
};
