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
        Schema::create('sales_invoice_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sales_invoice_id')->index();

            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->string('product_description')->nullable();

            $table->integer('quantity')->default(0);
            $table->decimal('unit_price', 12, 2)->default(0);

            $table->decimal('taxable_amount', 12, 2)->default(0);

            $table->string('hsn_code', 20)->nullable();
            $table->decimal('tax_rate', 5, 2)->default(0);

            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_with_tax', 12, 2)->default(0);

            $table->timestamps();

            // ===== RELATIONS =====
            $table->foreign('sales_invoice_id')
                ->references('id')
                ->on('sales_invoices')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->nullOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoice_items');
    }
};
