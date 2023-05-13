<?php

use Illuminate\Database\Seeder;

use App\Services\HeadToHead as Service;
use App\Models\HeadToHead as Model;

class RefreshUsersStats extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usersCollection = \App\Models\User::query()->with('userStat')->get();
        foreach ($usersCollection as $user) {
            if(!$user->userStat) {
                $user->userStat = new \App\Models\UserStat();
                $user->userStat->user_id = $user->id;
            }
            $user->userStat->hth_win = Service::getWinHthGames($user->id, Model::GAME_TYPE_SINGLE);
            $user->userStat->hth_lose = Service::getLoseHthGames($user->id, Model::GAME_TYPE_SINGLE);
            $user->userStat->dares_win = Service::getWinHthGames($user->id, Model::GAME_TYPE_DARE);
            $user->userStat->dares_lose = Service::getLoseHthGames($user->id, Model::GAME_TYPE_DARE);
            $user->userStat->save();
        }
    }
}
