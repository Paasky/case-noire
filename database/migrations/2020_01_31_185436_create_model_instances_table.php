<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModelInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_instances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('agency_case_id')->index();
            $table->bigInteger('model_id')->index();
            $table->string('model_type', 255)->index();
            $table->bigInteger('location_id')->nullable()->index();
            $table->point('coords')->nullable()->index();
            $table->float('lat', 13, 10)->nullable()->index();
            $table->float('lng', 13, 10)->nullable()->index();
            $table->string('status', 255)->nullable()->index();
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
        Schema::dropIfExists('model_instances');
    }
}