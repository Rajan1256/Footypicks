<?php

use Illuminate\Database\Seeder;

class ClearAllGame extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        \App\Models\UserInGame::truncate();
        \App\Models\Game::truncate();
        Schema::enableForeignKeyConstraints();
    }
}
