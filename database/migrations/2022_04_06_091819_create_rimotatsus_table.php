<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rimotatsus', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('champion_id')->default(-1);
            $table->integer('champion_num')->default(-1);
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
        Schema::dropIfExists('rimotatsus');
    }
};
