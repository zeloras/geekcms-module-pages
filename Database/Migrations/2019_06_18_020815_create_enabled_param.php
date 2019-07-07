<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnabledParam extends Migration
{
    public function up()
    {
        Schema::table('page_block_assings', function (Blueprint $table) {
            $table->boolean('enabled')->default(false);
            $table->increments('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('page_block_assings', function (Blueprint $table) {
            $table->dropColumn('enabled');
        });
    }
}
