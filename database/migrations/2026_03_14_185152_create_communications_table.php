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
        Schema::create('communications', function (Blueprint $table) {
            $table->id();

            $table->morphs('model'); // PurchaseOrder, SalesOrder

            $table->string('from_email');
            $table->json('to_emails');
            $table->json('cc_emails')->nullable();

            $table->string('subject');
            $table->longText('body');

            $table->json('attachments')->nullable();

            $table->foreignId('sent_by')->nullable()->constrained('users');
            $table->timestamp('sent_at')->nullable();

            $table->string('status')->default('sent'); // sent, failed

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};
