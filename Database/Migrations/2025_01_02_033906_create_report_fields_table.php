<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('minireportb1_report_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('minireportb1_reports')->onDelete('cascade');
            $table->integer('business_id')->nullable();
            $table->string('table_name');
            $table->string('field_name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('minireportb1_report_fields');
    }
};