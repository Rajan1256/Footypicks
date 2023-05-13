<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDataToApi2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->unsignedInteger('parse_id_v2');
            $table->string('season')->default('');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->unsignedInteger('parse_id_v2');
            $table->string('recent_form')->default('');
            $table->string('country')->default('');
        });

        Schema::table('players', function (Blueprint $table) {
            $table->unsignedInteger('parse_id');
            $table->dropColumn('date_birth');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('recent_form');
            $table->dropColumn('country');
            $table->dropColumn('parse_id_v2');
        });

        Schema::table('leagues', function (Blueprint $table) {
            $table->dropColumn('parse_id_v2');
            $table->dropColumn('season');
        });

        Schema::table('players', function (Blueprint $table) {
            $table->date('date_birth');
            $table->dropColumn('parse_id');
        });
    }
}
