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
        Schema::create('sales_proforma_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('sales_proforma_id')->constrained();

            $table->unsignedBigInteger('product_id')->nullable()->index();

            $table->integer('quantity')->default(0);
            $table->decimal('unit_price', 12, 2)->default(0);

            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);

            $table->decimal('taxable_amount', 12, 2)->default(0);

            $table->decimal('tax_rate', 5, 2)->default(0);

            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_with_tax', 12, 2)->default(0);

            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_proforma_items');
    }
};
