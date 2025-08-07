<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('easy_ticket_id');
            $table->string('display_ticket_id');
            $table->foreignId('company_id')->constrained('companies');
            $table->integer('project_id')->constrained('projects');
            $table->integer('created_by')->constrained('users');
            $table->dateTime('created_on');
            $table->string('heading');
            $table->text('description');
            $table->enum('priority', ['critical', 'high', 'medium', 'low'])->default('low');
            $table->enum('status', ['new', 'inprogress', 'onhold', 'resolved', 'deleted'])->default('new');
            $table->dateTime('taken_on')->nullable();
            $table->integer('owned_by')->nullable();
            $table->dateTime('resolved_on')->nullable();
            $table->integer('resolved_by')->nullable();
            $table->integer('time_worked')->nullable();
            $table->tinyInteger('attachment_present')->default(0)->comment('0:no|1:yes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
