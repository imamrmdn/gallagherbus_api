<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHalteModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('halte', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('koridor_id');
            $table->string('halte_name');
            $table->string('arrival_time_in_halte');
            $table->string('departure_time_in_halte');
            $table->string('deleted_at')->nullable();
            $table->timestamps();

            //
            $table->foreign('koridor_id')->references('id')->on('koridors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('halte');
    }
}