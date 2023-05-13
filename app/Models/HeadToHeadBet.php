<?php

namespace App\Models;

class HeadToHeadBet extends Base
{
    const TYPE_LOSE = 0;
    const TYPE_DRAW = 1;
    const TYPE_WIN = 2;

    const STATUS_INVITED = 2;
    const STATUS_FINISH = 5;

    protected $fillable = array(
        'user_id',
        'head_to_head_id',
        'goals_home_team',
        'goals_away_team',
        'team_id',
        'type'
    );

    protected $hidden = [
        'id',
       // 'user_id',
        'created_at',
        'updated_at',
    ];

    public function headToHead()
    {
        return $this->belongsTo(HeadToHead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    protected function getTeamIdAttribute($teamId = '')
    {
        return (int) $teamId;
    }
}
