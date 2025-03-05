<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMiniReportB1FilesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('minireportb1_files')) {
            Schema::create('minireportb1_files', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('business_id'); // Use `unsignedInteger` for normal integer
                $table->string('file_name');
                $table->unsignedBigInteger('parent_id');
                $table->json('layout')->nullable(); // Add the `layout` column here
                $table->timestamps();

                // Foreign key constraints
                $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
                $table->foreign('parent_id')->references('id')->on('minireportb1_folders')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('minireportb1_files');
    }
}