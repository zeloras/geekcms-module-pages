<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditPageParamsAndBlocks extends Migration
{
    public function up()
    {
        Schema::table('page_blocks', function (Blueprint $table) {
            $table->string('lang', 10)->nullable()->index()->after('content');
            $table->integer('parent_id')->default(0)->index()->after('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('page_blocks', function (Blueprint $table) {
            $table->dropColumn('lang');
            $table->dropColumn('parent_id');
        });
    }
}
