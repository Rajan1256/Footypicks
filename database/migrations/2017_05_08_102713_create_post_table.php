<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('title');
            $table->string('info')->default('');
            $table->string('cover')->default('');
            $table->date('date_from');
            $table->date('date_to');
            $table->time('time_from');
            $table->time('time_to');
            $table->string('address');
            $table->unsignedInteger('category_id');
            $table->boolean('is_private')->default(false);
            $table->unsignedInteger('number_extra_tickets')->default(0);
            $table->string('lat');
            $table->string('lng');
            $table->timestamps();
            $table->tinyInteger('status')->default(0); //0 -unpublished, 1 - published, 2 - hidden
        });

        Schema::table('articles', function(Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign('articles_category_id_foreign');
        });
        Schema::dropIfExists('articles');
        Schema::dropIfExists('categories');
    }
}
