<?php

namespace App\Models;

class Bet extends Base
{
    const TYPE_LOSE = 0;
    const TYPE_DRAW = 1;
    const TYPE_WIN = 2;


    const DELETED = 0;
    const LOSE = 1;
    const WIN = 2;
    const IN_GAME = 3;

    protected $appends = [
        'result'
    ];

    protected $fillable = array(
        'game_id',
        'user_id',
        'team_id',
        'schedule_id',
        'type',
	'wager_amount',
	'wager_currency',
	'is_wager'
    );

    protected $hidden = [
        'game_id',
        'user_id',
        'schedule_id',
        'created_at',
        'updated_at',
        'status'
    ];

    public function getResultAttribute() {
        return $this->status;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
