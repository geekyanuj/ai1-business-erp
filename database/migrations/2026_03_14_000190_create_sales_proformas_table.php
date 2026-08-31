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
        Schema::create('sales_proformas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('client_id')->index();
            $table->foreignId('quotation_id')->nullable()->unique()->constrained('sales_quotations')->cascadeOnDelete();

            $table->string('proforma_number')->unique();
            $table->date('proforma_date')->nullable();
            $table->string('client_po_ref')->nullable();
            $table->string('client_po_pdf')->nullable();

            $table->enum('status', ['draft', 'sent', 'issued', 'accepted', 'converted', 'cancelled'])
                ->default('draft');

            $table->decimal('subtotal', 12, 2)->default(0);

            $table->enum('tax_type', ['cgst_sgst', 'igst']);

            $table->decimal('cgst_amount', 12, 2)->default(0);
            $table->decimal('sgst_amount', 12, 2)->default(0);
            $table->decimal('igst_amount', 12, 2)->default(0);

            $table->decimal('grand_total', 12, 2)->default(0);

            $table->foreignId('created_by')->constrained('users');
            $table->string('client_query_from')->nullable();

            $table->text('notes')->nullable();
            $table->text('tnc')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_proformas');
    }
};
