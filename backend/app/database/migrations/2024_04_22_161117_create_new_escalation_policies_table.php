<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewEscalationPoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_escalation_policies', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('business_hour_id')->constrained('companies');
            $table->foreignId('group_id')->constrained('companies');
            $table->string('name');
            $table->tinyInteger('status')->default(0)->comment('0:inactive|1:active');
            $table->integer('resolution_time');
            $table->tinyInteger('resolution_time_unit')->default(0)->comment('0:seconds|1:mins|2:hours|3:days|4:months');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('new_escalation_policies');
    }
}
