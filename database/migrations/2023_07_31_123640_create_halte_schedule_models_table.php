<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHalteScheduleModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('halte_schedule', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('halte_id');
            $table->string('bus_queue');
            $table->string('bus_name');
            $table->string('arrival_time_bus');
            $table->string('departure_time_bus');
            $table->string('deleted_at')->nullable();
            $table->timestamps();

            //
            $table->foreign('halte_id')->references('id')->on('halte');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('halte_schedule');
    }
}
