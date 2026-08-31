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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inventory_id')
                ->constrained('inventories')
                ->cascadeOnDelete();

            $table->enum('movement_type', [
                'purchase',
                'opening',
                'grn',
                'sale',
                'production_in',
                'production_out',
                'adjustment',
                'transfer',
                'return'
            ]);

            $table->integer('quantity');
            $table->string('reference_type')->nullable(); // invoice, po, production
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->index(['reference_type', 'reference_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
