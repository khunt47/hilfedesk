<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCommentsTableAddCreatedUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->integer('customer_id')->default(0);
            $table->integer('contact_id')->default(0);
            $table->tinyInteger('created_user')->default(1)->comment("1:user|2:customer");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('customer_id');
            $table->dropColumn('contact_id');
            $table->dropColumn('created_user');
        });
    }
}
