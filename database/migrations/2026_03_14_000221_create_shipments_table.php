<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentsTable extends Migration
{
    public function up()
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sales_invoice_id')->index();
            $table->string('shipment_no')->unique();
            $table->date('shipped_date')->nullable();
            $table->string('courier_name')->nullable();
            $table->string('tracking_no')->nullable()->index();
            $table->unsignedBigInteger('shipped_by')->nullable()->index();
            $table->timestamps();

            $table->foreign('sales_invoice_id')->references('id')->on('sales_invoices')->onDelete('cascade');
            $table->foreign('shipped_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipments');
    }
}
