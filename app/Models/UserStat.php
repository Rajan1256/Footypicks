<?php

namespace App\Models;


/**
 * User Stat Model
 *
 * @property int $id
 * @property int $user_id
 * @property int $games_win
 * @property int $games_lose
 * @property int $hth_win
 * @property int $hth_lose
 * @property int $dares_win
 * @property int $dares_lose
 *
 *
 * @property user User
 *
 * @package App\Models
 */
class UserStat extends Base
{
    const TYPE_HTH = 'hth';
    const TYPE_DARE = 'dares';
    const TYPE_GAME= 'games';

    public $timestamps = false;

    protected $fillable = array(
        'games_win',
        'games_lose',
        'hth_win',
        'hth_lose',
        'dares_win',
        'dares_lose',
    );

    protected $hidden = [
        'id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function incUserStats($userId, $type = self::TYPE_HTH, $isWin = false)
    {
        $field = $type . '_';
        $field .= ($isWin === true) ? 'win' : 'lose';
        $model = self::query()->where('user_id', $userId)->first();
        if(!isset($model->id)) {
            $model = new self();
            $model->user_id = $userId;
        }

        $model->$field = (int)$model->$field + 1;
        return $model->save();
    }
}
