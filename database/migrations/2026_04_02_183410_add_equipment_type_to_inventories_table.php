<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddEquipmentTypeToInventoriesTable extends Migration
{
    public function up()
    {
        // MySQL: modify enum to add 'equipment'
        DB::statement("ALTER TABLE inventories MODIFY COLUMN inventory_type ENUM('raw','ready','equipment') NOT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE inventories MODIFY COLUMN inventory_type ENUM('raw','ready') NOT NULL");
    }
}
