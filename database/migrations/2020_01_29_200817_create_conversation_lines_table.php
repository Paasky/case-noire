<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversation_lines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('conversation_id')->index();
            $table->bigInteger('from_line_id')->nullable()->index();
            $table->string('said_by', 255)->index();
            $table->text('text');
            $table->string('audio_file', 255)->nullable();
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
        Schema::dropIfExists('conversation_lines');
    }
}
