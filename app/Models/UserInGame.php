<?php

namespace App\Models;

class UserInGame extends Base
{
    const NOT_CONFIRM_STATUS = 4;
    const STATUS_FINISH = 5;

    public $timestamps = false;

    protected $table = 'users_in_games';

    protected $fillable = array(
        'game_id',
        'user_id',
        'points',
        'status',
    );

    protected $hidden = [
        'game_id',
        'user_id',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
