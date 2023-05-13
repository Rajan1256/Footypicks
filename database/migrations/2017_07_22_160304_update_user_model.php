<?php


use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserModel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table)
        {
            $table->dropColumn('sex');
            $table->dropColumn('age');
            $table->string('nickname')->unique();
            $table->date('dt_birthday');
            $table->string('favorite_team');
            $table->boolean('push_notification')->default(false);
            $table->boolean('show_profile')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $table)
        {
            return false;
        });
    }
}
