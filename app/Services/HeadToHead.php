<?php

namespace App\Services;

use App\Models\HeadToHead as Model;

class HeadToHead extends Base
{

    protected $orderBy = [];

    protected $modelName = Model::class;

    public static function getWinHthGames($userId, $gameType = Model::GAME_TYPE_DARE)
    {
        return Model::query()
            ->where('game_type', $gameType)
            ->where('status', Model::STATUS_FINISH)
            ->where('win_user_id', $userId)
            ->count('id');
    }

    public static function getLoseHthGames($userId, $gameType = Model::GAME_TYPE_DARE)
    {
        return Model::query()
            ->where('game_type', Model::GAME_TYPE_DARE)
            ->where('status', Model::STATUS_FINISH)
            ->where('win_user_id', '!=', $userId)
            ->where(function ($query) use ($userId){
                $query->where('user_id', $userId);
                $query->orWhere(function ($query) use ($userId){
                    $query->whereHas('invite', function ($query) use ($userId) {
                        $query->where('user_id', $userId)
                            ->where('status', Model::ACTIVE);
                    });
                });
                $query->orWhere(function ($query) use ($userId){
                    $query->whereHas('bet', function ($query) use ($userId) {
                        $query->where('user_id', $userId);
                    });
                });
            })
            ->count('id');
    }
}
