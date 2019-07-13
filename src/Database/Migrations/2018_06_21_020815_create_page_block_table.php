<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageBlockTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('page_blocks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->text('content')->nullable();
            $table->timestamps();
        });

        Schema::create('page_block_assings', function (Blueprint $table) {
            $table->integer('page_id')->index();
            $table->integer('block_id')->index();
            $table->integer('position')->index();
            $table->timestamps();
        });

        Schema::create('page_block_variables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('block_id')->index();
            $table->string('type')->nullable();
            $table->string('key')->index();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('page_blocks');
        Schema::dropIfExists('page_block_assings');
        Schema::dropIfExists('page_block_variables');
    }
}
