<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 32)->change();
            $table->string('password', 100)->nullable()->change();

            $table->string('role', 12)->after('remember_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // We should not shorten the column length.

            $table->string('password', 100)->nullable(false)->change();

            $table->dropColumn('role');
        });
    }
}
