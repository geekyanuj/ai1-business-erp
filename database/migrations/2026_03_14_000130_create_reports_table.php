<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('report_type', 100);
            $table->unsignedBigInteger('generated_by')->nullable()->index();
            $table->dateTime('generated_at')->nullable();
            $table->text('parameters')->nullable(); // serialized params
            $table->longText('result')->nullable(); // serialized report result (JSON, CSV, etc.)
            $table->timestamps();

            $table->foreign('generated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
}
