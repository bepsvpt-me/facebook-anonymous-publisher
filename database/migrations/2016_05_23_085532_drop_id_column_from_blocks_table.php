<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropIdColumnFromBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blocks', function (Blueprint $table) {
            $table->dropUnique(['type', 'value']);

            $table->dropColumn('id');

            $table->primary(['type', 'value']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blocks', function (Blueprint $table) {
            $table->dropPrimary(['type', 'value']);

            $table->unique(['type', 'value']);
        });

        Schema::table('blocks', function (Blueprint $table) {
            $table->increments('id')->first();
        });
    }
}
