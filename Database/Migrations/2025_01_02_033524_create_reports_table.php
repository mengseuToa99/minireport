<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('minireportb1_reports')) {
            Schema::create('minireportb1_reports', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // Add the `name` column
                $table->integer('business_id')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('minireportb1_report_fields')) {
            Schema::table('minireportb1_report_fields', function (Blueprint $table) {
                $table->dropForeign(['report_id']); 
                $table->integer('business_id')->nullable()->change();
            });
        }

        Schema::dropIfExists('minireportb1_reports'); // Drop the `reports` table
    }
};