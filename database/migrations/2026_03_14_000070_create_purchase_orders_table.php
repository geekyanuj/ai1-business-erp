<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('supplier_id')->nullable();

            $table->enum('po_type', ['raw', 'ready'])->default('raw');
            $table->string('po_number')->unique();
            $table->string('quote_ref')->nullable();

            $table->enum('status', ['draft', 'approved', 'received', 'partial'])->default('draft');

            $table->date('ordered_date')->nullable();
            $table->date('approved_on')->nullable();
            $table->date('delivery_date')->nullable();

            // Deliver to address relation
            $table->unsignedBigInteger('deliver_to_id')->nullable();

            $table->date('received_date')->nullable();

            $table->text('remarks')->nullable();
            $table->text('tnc')->nullable();
            $table->text('notes')->nullable();

            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('approved_by')->nullable()->index();
            $table->unsignedBigInteger('received_by')->nullable()->index();

            $table->timestamps();

            $table->decimal('subtotal', 12, 2)->default(0);

            $table->enum('tax_type', ['cgst_sgst', 'igst']);

            $table->decimal('cgst_amount', 12, 2)->default(0);
            $table->decimal('sgst_amount', 12, 2)->default(0);
            $table->decimal('igst_amount', 12, 2)->default(0);

            $table->decimal('grand_total', 12, 2)->default(0);

            // Foreign Keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('received_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');

            $table->foreign('deliver_to_id')
                ->references('id')
                ->on('addresses')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
}