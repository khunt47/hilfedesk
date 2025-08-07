<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropGeedeskCompanyMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('geedesk_company_mapping');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('geedesk_company_mapping', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('cust_id');
            $table->integer('geedesk_company_id');
        });
    }
}
