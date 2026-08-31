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
        Schema::create('sales_quotations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('client_id')->index();
            $table->string('quotation_number')->unique();
            $table->date('quotation_date')->nullable();

            $table->enum('status', ['draft', 'sent', 'accepted', 'converted', 'rejected'])->default('draft');
            $table->string('client_query_from')->nullable();

            $table->text('notes')->nullable();
            $table->text('tnc')->nullable();
            $table->text('remarks')->nullable();


            // Totals AFTER item-level discount
            $table->decimal('subtotal', 12, 2)->default(0);

            $table->enum('tax_type', ['cgst_sgst', 'igst'])->nullable();

            $table->decimal('cgst_amount', 12, 2)->default(0);
            $table->decimal('sgst_amount', 12, 2)->default(0);
            $table->decimal('igst_amount', 12, 2)->default(0);

            $table->decimal('grand_total', 12, 2)->default(0);

            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();

            $table->foreign('client_id')
                ->references('id')->on('clients')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')->on('users')
                ->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_quotations');
    }
};
