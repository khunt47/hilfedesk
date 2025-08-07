<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropTimezonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('timezones');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        SChema::create('timezones', function(Blueprint $table) {
            $table->increments('id');
            $table->text('country_code');
            $table->text('country_name');
            $table->text('timezone');
            $table->text('utc_offset');
        });
    }
}
