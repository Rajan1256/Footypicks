<?php

namespace App\Models;

class Game extends Base
{
    const FINISH_STATUS = 4;

    const GAME_STATUS = [
        0 => 'NOT ACTIVE',
        1 => 'ACTIVE',
        3 => 'DELETE',
        4 => 'FINISH',
    ];

    protected $fillable = array(
        'league_id',
        'user_id',
        'name',
        'status',
    );

    protected $hidden = [
        'league_id',
        'user_id',
        'updated_at',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function league()
    {
        return $this->belongsTo(League::class);
    }

    public function prepareStringStatus()
    {
        return $this::GAME_STATUS[$this->status] ?? $this::GAME_STATUS[$this::NOT_ACTIVE];
    }

    public function connect()
    {
        return $this->hasMany(UserInGame::class);
    }

    public function bet()
    {
        return $this->hasMany(Bet::class, 'game_id', 'id');
    }

    public function prepareNotificationMessageCreate()
    {
        return 'You created a new "' . $this->name . '" League';
    }

    public function prepareNotificationMessageWin()
    {
        return 'You win "' . $this->name . '" League';
    }

    public function prepareNotificationMessageLose()
    {
        return 'You lose "' . $this->name . '" League';
    }

    public function prepareNotificationMessageTie()
    {
        return 'League "' . $this->name . '" is tie';
    }

    public function prepareInviteNotificationMessage()
    {
        return  'You are invited to "' . $this->name . '" League';
    }
}
