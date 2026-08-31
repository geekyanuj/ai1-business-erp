<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE products MODIFY category ENUM('RF Antenna','RF Cable Assembly','RF Cable','Microwave Devices','IoT') NOT NULL");

        Schema::table('labels', function (Blueprint $table) {
            $table->foreignId('production_batch_id')
                ->nullable()
                ->after('category')
                ->constrained('production_batches')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('labels', function (Blueprint $table) {
            $table->dropForeign(['production_batch_id']);
            $table->dropColumn('production_batch_id');
        });

        DB::statement("ALTER TABLE products MODIFY category ENUM('RF Antenna','RF Cable Assembly','Microwave Devices','IoT') NOT NULL");
    }
};