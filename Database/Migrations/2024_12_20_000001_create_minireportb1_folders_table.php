<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('minireportb1_folders')) {
            Schema::create('minireportb1_folders', function (Blueprint $table) {
                $table->id();
                $table->integer('business_id')->unsigned();
                $table->string("folder_name");
                $table->string("type")->default('report_section');
                $table->integer("order")->default(0);
                $table->integer('parent_id')->default(0);
                $table->boolean('is_parent')->default(false);
                $table->timestamps();

                // Add index for business_id
                $table->index('business_id');

                // Add unique constraint for folder name within same business
                $table->unique(['business_id', 'folder_name']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('minireportb1_folders');
    }
};