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
        Schema::create('qc_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_name')->nullable();
            $table->integer('total')->default(0);
            $table->integer('processed')->default(0);
            $table->integer('failed')->default(0);
            $table->enum('status', ['processing', 'completed', 'failed'])->default('processing');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qc_batches');
    }
};
