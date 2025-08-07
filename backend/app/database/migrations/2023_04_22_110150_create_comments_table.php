<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('company_id')->constrained('companies');
            $table->integer('ticket_id')->constrained('tickets');
            $table->integer('created_by')->constrained('users');
            $table->dateTime('created_on');
            $table->enum('status', ['published', 'deleted'])->default('published');
            $table->enum('public', ['yes', 'no'])->default('yes');
            $table->enum('attachment', ['yes', 'no'])->default('no');
            $table->text('comment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
