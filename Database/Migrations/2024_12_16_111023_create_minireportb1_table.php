<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMiniReportB1Table extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('minireportb1_main')) {
            Schema::create('minireportb1_main', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_id');
                $table->unsignedInteger('created_by')->nullable();
                $table->date('Date_1')->nullable();
                $table->string('Title_1')->nullable();
                $table->unsignedInteger('category_id')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('minireportb1_main'); // Fixed table name
    }
}