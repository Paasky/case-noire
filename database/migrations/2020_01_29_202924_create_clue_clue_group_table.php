<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClueClueGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clue_clue_group', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('clue_id')->index();
            $table->bigInteger('clue_group_id')->index();
            $table->timestamps();
            $table->unique(['clue_id', 'clue_group_id'], 'clue_clue_group_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clue_clue_group');
    }
}
