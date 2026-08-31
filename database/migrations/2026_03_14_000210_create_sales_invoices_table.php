<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');

            // ===== RELATIONS =====
            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();

            $table->foreignId('quotation_id')
                ->nullable()
                ->unique()
                ->constrained('sales_quotations')
                ->nullOnDelete();

            $table->foreignId('proforma_id')
                ->nullable()
                ->unique()
                ->constrained('sales_proformas')
                ->nullOnDelete();

            // ===== DOCUMENT INFO =====
            $table->string('invoice_number')->unique();
            $table->date('invoice_date')->nullable();

            $table->enum('status', [
                'draft',
                'issued',
                'paid',
                'partially_paid',
                'cancelled'
            ])->default('draft');

            $table->string('client_po_ref')->nullable();
            $table->string('client_po_pdf')->nullable();

            // ===== PAYMENT & NOTES =====
            $table->string('payment_mode')->nullable();
            $table->text('remarks')->nullable();
            $table->text('tnc')->nullable();
            $table->text('notes')->nullable();

            // ===== AMOUNTS =====
            $table->decimal('subtotal', 12, 2)->default(0);

            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance_amount', 12, 2)->default(0);

            // ===== GST STRUCTURE =====
            $table->enum('tax_type', ['cgst_sgst', 'igst']);

            $table->decimal('cgst_amount', 12, 2)->default(0);
            $table->decimal('sgst_amount', 12, 2)->default(0);
            $table->decimal('igst_amount', 12, 2)->default(0);

            $table->decimal('grand_total', 12, 2)->default(0);

            // ===== AUDIT =====
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_invoices');
    }
};
