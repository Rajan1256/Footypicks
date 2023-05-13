<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPushTokenToUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('push_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token',255);
            $table->unsignedInteger('user_id');
            $table->timestamps();
        });

        Schema::table('push_tokens', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('push_tokens', function (Blueprint $table) {
            $table->dropForeign('push_tokens_user_id_foreign');
        });
        Schema::dropIfExists('push_tokens');
    }
}
