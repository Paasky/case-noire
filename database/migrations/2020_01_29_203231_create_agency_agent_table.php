<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgencyAgentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agency_agent', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('agency_id')->index();
            $table->bigInteger('agent_id')->index();
            $table->enum('type', \App\Constants\AgentConst::TYPES)->index();
            $table->timestamps();
            $table->unique(['agency_id', 'agent_id'], 'agency_agent_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agency_agent');
    }
}
