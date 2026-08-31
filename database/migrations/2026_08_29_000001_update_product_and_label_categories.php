<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $categories = [
        'RF Antenna',
        'RF Cable Assembly',
        'Microwave Devices',
        'IoT',
        'RF Cable',
    ];

    public function up(): void
    {
        DB::statement("ALTER TABLE products MODIFY category ENUM('RF Antenna','RF Cable','RF Cable Assembly','Microwave Device','Microwave Devices','IoT') NOT NULL");

        DB::table('products')->where('category', 'RF Cable')->update(['category' => 'RF Cable Assembly']);
        DB::table('products')->where('category', 'Microwave Device')->update(['category' => 'Microwave Devices']);

        $categories = implode("','", $this->categories);
        DB::statement("ALTER TABLE products MODIFY category ENUM('$categories') NOT NULL");

        Schema::table('labels', function (Blueprint $table) {
            $table->string('category')->nullable()->after('client_id')->index();
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE products MODIFY category ENUM('RF Antenna','RF Cable','Microwave Device','IoT','RF Cable Assembly','Microwave Devices') NOT NULL");
        DB::table('products')->where('category', 'RF Cable Assembly')->update(['category' => 'RF Cable']);
        DB::table('products')->where('category', 'Microwave Devices')->update(['category' => 'Microwave Device']);

        DB::statement("ALTER TABLE products MODIFY category ENUM('RF Antenna','RF Cable','Microwave Device','IoT') NOT NULL");
        Schema::table('labels', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};