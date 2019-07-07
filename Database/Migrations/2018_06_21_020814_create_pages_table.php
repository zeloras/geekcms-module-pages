<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $types = GeekCms\Pages\Models\Page::$types;

            $table->increments('id');
            $table->integer('parent_id')->default(0)->index();
            $table->string('lang', 10)->nullable()->index();
            $table->enum('type', $types)->default('page');
            $table->string('theme')->nullable();
            $table->string('name', 50)->unique();
            $table->string('slug', 50);
            $table->text('content')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('pages');
    }
}
