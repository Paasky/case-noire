<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('case_template_id')->index();
            $table->bigInteger('fired_by_event_id')->nullable()->index();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->text('image_url')->nullable();
            $table->integer('timer')->nullable();
            $table->json('location_settings');
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
        Schema::dropIfExists('events');
    }
}
