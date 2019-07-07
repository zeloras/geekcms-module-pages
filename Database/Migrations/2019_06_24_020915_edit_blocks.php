<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditBlocks extends Migration
{
    public function up()
    {
        Schema::table('page_blocks', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesFound = $sm->listTableIndexes('page_blocks');
            if (array_key_exists('page_blocks_name_unique', $indexesFound)) {
                $table->dropIndex('page_blocks_name_unique');
            }

            if (array_key_exists('name', $indexesFound)) {
                $table->dropIndex('name');
            }

            $table->unique(['name', 'lang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('page_blocks', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesFound = $sm->listTableIndexes('page_blocks');
            if (array_key_exists('page_blocks_name_unique', $indexesFound)) {
                $table->dropIndex('page_blocks_name_unique');
            }

            if (array_key_exists('name', $indexesFound)) {
                $table->dropIndex('name');
            }

            if (array_key_exists('page_blocks_name_lang_unique', $indexesFound)) {
                $table->dropIndex('page_blocks_name_lang_unique');
            }

            $table->unique('name')->change();
        });
    }
}
