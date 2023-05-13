<?php

use App\Models\HeadToHead;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateHthBetType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('head_to_heads', function (Blueprint $table) {
            $table->boolean('is_pick')->default(HeadToHead::BET_STATUS_PICK);
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
            $table->dropColumn('is_pick');
        });
    }
}
