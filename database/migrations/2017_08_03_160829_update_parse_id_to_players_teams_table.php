<?php

use App\Models\Base;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateParseIdToPlayersTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->unsignedInteger('parse_id');
            $table->tinyInteger('status')->default(Base::ACTIVE);
        });

        Schema::table('players', function (Blueprint $table) {
            $table->tinyInteger('status')->default(Base::ACTIVE);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teams', function(Blueprint $table)
        {
            $table->dropColumn('parse_id');
            $table->dropColumn('status');
        });

        Schema::table('players', function(Blueprint $table)
        {
            $table->dropColumn('status');
        });
    }
}
