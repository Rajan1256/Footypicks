<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserFeedCountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user__feed__counts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('post_id')->unsigned();
            //$table->foreign('post_id')->references('id')->on('post_comments');
            $table->integer('post_user_id')->unsigned()->nullable();
            $table->foreign('post_user_id')->references('user_id')->on('post_comments');
            $table->integer('like_user_id')->unsigned()->nullable();
            $table->foreign('like_user_id')->references('user_id')->on('post_likes');
            $table->integer('follow_user_id')->unsigned()->nullable();
            $table->foreign('follow_user_id')->references('follow_id')->on('user_follows');
//            $table->integer('follow_user_id')->unsigned();
//            $table->foreign('follow_user_id')->references('follow_id')-s>on('user_follows');
            $table->integer('is_read')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user__feed__counts');
    }
}
