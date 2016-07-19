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
        if ('sqlite' !== DB::connection()->getDriverName()) {
            Schema::table('blocks', function (Blueprint $table) {
                $table->dropUnique(['type', 'value']);

                $table->dropColumn('id');

                $table->primary(['type', 'value']);
            });
        } else {
            Schema::drop('blocks');

            Schema::create('blocks', function (Blueprint $table) {
                $table->string('type', 24);
                $table->string('value', 48);

                $table->primary(['type', 'value']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if ('sqlite' !== DB::connection()->getDriverName()) {
            Schema::table('blocks', function (Blueprint $table) {
                $table->dropPrimary(['type', 'value']);

                $table->unique(['type', 'value']);
            });

            Schema::table('blocks', function (Blueprint $table) {
                $table->increments('id')->first();
            });
        } else {
            Schema::drop('blocks');

            Schema::create('blocks', function (Blueprint $table) {
                $table->increments('id');
                $table->string('type', 24);
                $table->string('value', 48);

                $table->unique(['type', 'value']);
            });
        }
    }
}
