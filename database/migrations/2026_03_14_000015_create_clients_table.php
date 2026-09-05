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
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->unique(); // No duplicate client names
            $table->string('contact_person')->nullable();

            // Email can be null many times, unique only when not null
            $table->string('email')->nullable();
            $table->unique('email');

            $table->string('phone')->nullable();

            // Address relations
            $table->unsignedBigInteger('billing_address_id')->nullable();
            $table->unsignedBigInteger('shipping_address_id')->nullable();

            $table->string('gst_number', 30)->nullable();
            $table->text('notes')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Foreign keys
            $table->foreign('billing_address_id')
                ->references('id')
                ->on('addresses')
                ->onDelete('set null');

            $table->foreign('shipping_address_id')
                ->references('id')
                ->on('addresses')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};