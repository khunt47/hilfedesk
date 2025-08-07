<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_hours', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('company_id')->constrained('companies');
            $table->string('name');
            $table->tinyInteger('247')->default(0)->comment('0:no|1:yes');
            $table->time('sunday_start', $precision = 0);
            $table->time('sunday_end', $precision = 0);
            $table->time('monday_start', $precision = 0);
            $table->time('monday_end', $precision = 0);
            $table->time('tuesday_start', $precision = 0);
            $table->time('tuesday_end', $precision = 0);
            $table->time('wednesday_start', $precision = 0);
            $table->time('wednesday_end', $precision = 0);
            $table->time('thursday_start', $precision = 0);
            $table->time('thursday_end', $precision = 0);
            $table->time('friday_start', $precision = 0);
            $table->time('friday_end', $precision = 0);
            $table->time('saturday_start', $precision = 0);
            $table->time('saturday_end', $precision = 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_hours');
    }
}
