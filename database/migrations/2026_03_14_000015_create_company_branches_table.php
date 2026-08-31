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
        Schema::create('company_branches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            // Identity
            $table->string('name');                 // HQ, Factory, Warehouse
            $table->string('branch_code')->nullable(); // BLR, CHN, MUM

            // GST & TAX
            $table->string('gst_number')->nullable();
            $table->string('state_code', 2)->nullable(); // 29, 33, etc.

             // Address (MANDATORY for ERP)
            $table->text('address_line1');
            $table->text('address_line2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('pincode');
            $table->string('country')->default('India');

            // Contact
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            // Control
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Prevent duplicate branch names per company
            $table->unique(['company_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_branches');
    }
};
