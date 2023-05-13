<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostCountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_counts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->unsigned();
            $table->foreign('post_id')->references('id')->on('feeds')->onDelete('cascade');
            $table->integer('o_user_id')->unsigned();
            $table->foreign('o_user_id')->references('id')->on('users');
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
        Schema::dropIfExists('post_counts');
    }
}
