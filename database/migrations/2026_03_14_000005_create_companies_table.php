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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('name'); // Legal Company Name
            $table->string('company_code')->nullable()->unique(); // TE, TEXMIN, etc.

             // Compliance (optional but ERP-ready)
            $table->string('pan_number')->nullable();
            $table->string('cin_number')->nullable();   // Corporate ID (optional)
            $table->string('iec_number')->nullable();   // Import Export (optional)

            // Communication (HQ-level, not tax)
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

             // Branding
            $table->string('logo')->nullable();
            $table->string('authorised_signature')->nullable();

            // Control
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
