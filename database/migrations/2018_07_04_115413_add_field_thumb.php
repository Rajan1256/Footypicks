<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldThumb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('feeds', function (Blueprint $table) {
            $table->string('feed_thumb')->after("feed_file");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('feeds', function (Blueprint $table) {
            $table->dropColumn('feed_thumb');
        });
    }
}
