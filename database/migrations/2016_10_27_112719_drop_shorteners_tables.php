<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropShortenersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('shorteners');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('shorteners', function (Blueprint $table) {
            $table->increments('id');
            $table->char('hash', 128)->unique();
            $table->string('url', 4096);
            $table->timestamp('created_at')->nullable();
        });
    }
}
