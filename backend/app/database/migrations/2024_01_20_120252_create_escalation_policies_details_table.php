<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEscalationPoliciesDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('escalation_policies_details', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('company_id')->constrained('companies');
            $table->integer('esc_policy_id')->constrained('escalation_policies');
            $table->integer('project_id')->constrained('projects');
            $table->float('p1_response_time');
            $table->enum('p1_response_time_unit', ['Mins','Hrs', 'Days', 'Mnths'])->default('Mins');
            $table->float('p1_resolution_time');
            $table->enum('p1_resolution_time_unit', ['Mins','Hrs', 'Days', 'Mnths'])->default('Mins');
            $table->float('p2_response_time');
            $table->enum('p2_response_time_unit', ['Mins','Hrs', 'Days', 'Mnths'])->default('Mins');
            $table->float('p2_resolution_time');
            $table->enum('p2_resolution_time_unit', ['Mins','Hrs', 'Days', 'Mnths'])->default('Mins');
            $table->float('p3_response_time');
            $table->enum('p3_response_time_unit', ['Mins','Hrs', 'Days', 'Mnths'])->default('Mins');
            $table->float('p3_resolution_time');
            $table->enum('p3_resolution_time_unit', ['Mins','Hrs', 'Days', 'Mnths'])->default('Mins');
            $table->float('p4_response_time');
            $table->enum('p4_response_time_unit', ['Mins','Hrs', 'Days', 'Mnths'])->default('Mins');
            $table->float('p4_resolution_time');
            $table->enum('p4_resolution_time_unit', ['Mins','Hrs', 'Days', 'Mnths'])->default('Mins');
            $table->integer('first_esc_group_id')->constrained('groups');
            $table->integer('sec_esc_group_id')->constrained('groups');
            $table->integer('third_esc_group_id')->constrained('groups');
            $table->integer('fourth_esc_group_id')->constrained('groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('escalation_policies_details');
    }
}
