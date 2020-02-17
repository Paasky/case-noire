<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCluesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->text('image_url')->nullable();
            $table->bigInteger('case_template_id')->index();
            $table->bigInteger('given_by_id')->index();
            $table->string('given_by_type', 255)->index();
            $table->bigInteger('evidence_id')->nullable()->index();
            $table->bigInteger('evidence_requirement_id')->nullable()->index();
            $table->string('evidence_requirement_type', 255)->nullable()->index();
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
        Schema::dropIfExists('clues');
    }
}
