<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventorySerialsTable extends Migration
{
    public function up()
    {
        Schema::create('inventory_serials', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inventory_id')
                ->constrained('inventories')
                ->cascadeOnDelete();

            $table->string('serial_number')->index();
            $table->enum('status', [
                'in_stock',
                'reserved',
                'sold',
                'returned',
                'scrapped'
            ])->default('in_stock');
            $table->string('supplier_serial_number')->index();
            $table->string('source_type');
            $table->string('source_id');


            $table->timestamps();

            $table->unique(['inventory_id', 'serial_number']);
        });

    }

    public function down()
    {
        Schema::dropIfExists('inventory_serials');
    }
}
