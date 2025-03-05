<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMiniReportB1CategoryTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('minireportb1_category')) {
            Schema::create('minireportb1_category', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedBigInteger('business_id');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('minireportb1_category'); // Fixed table name
    }
}