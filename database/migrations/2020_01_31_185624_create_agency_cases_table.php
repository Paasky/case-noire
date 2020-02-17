<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgencyCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agency_cases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('agency_id');
            $table->bigInteger('case_template_id');
            $table->bigInteger('location_id');
            $table->string('status', 255)->index();
            $table->json('data')->nullable();
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
        Schema::dropIfExists('agency_cases');
    }
}