<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeColumnToFirewallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('firewalls', function (Blueprint $table) {
            $table->enum('type', ['regular', 'permanent', 'segment'])
                ->default('regular')
                ->after('ip')
                ->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('firewalls', function (Blueprint $table) {
            $table->dropColumn(['type']);
        });
    }
}
