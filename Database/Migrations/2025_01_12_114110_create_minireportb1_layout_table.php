<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('minireportb1_layout', function (Blueprint $table) {
            $table->id();
            $table->string('layout_name'); // This will store the filename/layout name
            $table->string('type'); // Type of component (e.g., draggable1, draggable2)
            $table->json('content')->nullable(); // Content of the component
            $table->float('x')->default(0); // X coordinate
            $table->float('y')->default(0); // Y coordinate
            $table->float('position')->default(0); // For sequence ordering
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('minireportb1_layout');
    }
};
