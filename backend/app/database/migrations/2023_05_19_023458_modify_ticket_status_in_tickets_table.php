<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTicketStatusInTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            DB::statement("ALTER TABLE tickets MODIFY status ENUM ('new', 'inprogress', 'onhold', 'resolved', 'deleted', 'merged') NOT NULL DEFAULT 'new';");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            DB::statement("ALTER TABLE tickets MODIFY status ENUM ('new', 'inprogress', 'onhold', 'resolved', 'deleted') NOT NULL DEFAULT 'new';");
        });
    }
}
