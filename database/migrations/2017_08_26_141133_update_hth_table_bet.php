<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateHthTableBet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('head_to_heads', function (Blueprint $table) {
            $table->unsignedTinyInteger('game_type')->default(\App\Models\HeadToHead::GAME_TYPE_SINGLE);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('head_to_heads', function (Blueprint $table) {
            $table->dropColumn('game_type');
        });
    }
}
