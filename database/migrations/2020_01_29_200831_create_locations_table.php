<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('hash', 32)->unique();
            $table->string('source', 255)->index();
            $table->string('source_id', 255)->index();
            $table->point('coords')->index();
            $table->string('address', 255);
            $table->string('name', 255)->nullable();
            $table->text('description')->nullable();
            $table->text('image_url')->nullable();
            $table->text('link')->nullable();
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
        Schema::dropIfExists('locations');
    }
}
