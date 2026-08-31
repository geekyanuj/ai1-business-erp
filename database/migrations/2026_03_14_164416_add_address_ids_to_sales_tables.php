<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales_quotations', function (Blueprint $table) {
            $table->unsignedBigInteger('billing_address_id')->nullable()->after('client_id');
            $table->unsignedBigInteger('shipping_address_id')->nullable()->after('billing_address_id');
            $table->foreign('billing_address_id')->references('id')->on('addresses')->onDelete('set null');
            $table->foreign('shipping_address_id')->references('id')->on('addresses')->onDelete('set null');
        });

        Schema::table('sales_proformas', function (Blueprint $table) {
            $table->unsignedBigInteger('billing_address_id')->nullable()->after('client_id');
            $table->unsignedBigInteger('shipping_address_id')->nullable()->after('billing_address_id');
            $table->foreign('billing_address_id')->references('id')->on('addresses')->onDelete('set null');
            $table->foreign('shipping_address_id')->references('id')->on('addresses')->onDelete('set null');
        });

        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('billing_address_id')->nullable()->after('client_id');
            $table->unsignedBigInteger('shipping_address_id')->nullable()->after('billing_address_id');
            $table->foreign('billing_address_id')->references('id')->on('addresses')->onDelete('set null');
            $table->foreign('shipping_address_id')->references('id')->on('addresses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_quotations', function (Blueprint $table) {
            $table->dropForeign(['billing_address_id']);
            $table->dropForeign(['shipping_address_id']);
            $table->dropColumn(['billing_address_id', 'shipping_address_id']);
        });

        Schema::table('sales_proformas', function (Blueprint $table) {
            $table->dropForeign(['billing_address_id']);
            $table->dropForeign(['shipping_address_id']);
            $table->dropColumn(['billing_address_id', 'shipping_address_id']);
        });

        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropForeign(['billing_address_id']);
            $table->dropForeign(['shipping_address_id']);
            $table->dropColumn(['billing_address_id', 'shipping_address_id']);
        });
    }
};
