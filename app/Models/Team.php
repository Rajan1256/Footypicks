<?php

namespace App\Models;

class Team extends Base
{
    protected $fillable = array(
        'name',
        'cover',
        'played_games',
        'position',
        'points',
        'wins',
        'draws',
        'losses',
        'parse_id',
        'league_id',
        'parse_id_v2',
    );

    protected $hidden = [
        'status',
        'parse_id',
        'parse_id_v2',
        'created_at',
        'updated_at',
    ];

    public function getRecentFormAttribute($data = '')
    {
        $query = Schedule::query()->orderBy('date', 'DESC');
        $self = $this;
        $query->orWhere(function ($query) use ($self) {
            $query->where('team_away_id', $self->id)
                ->where('league_id', $self->league_id)
                ->where('status', Schedule::FINISHED);
        });

        $query->orWhere(function ($query) use ($self) {
            $query->where('team_home_id', $self->id)
                ->where('league_id', $self->league_id)
                ->where('status', Schedule::FINISHED);
        });

        $collections = $query->limit(6)->orderBy('date')->get();
        $value = "";
        foreach ($collections as $model) {
            $result = $model->getResultByTeamId($this->id);
            if($result == Bet::TYPE_DRAW) {
                $value .= 'D';
            }
            if($result == Bet::TYPE_LOSE) {
                $value .= 'L';
            }
            if($result == Bet::WIN) {
                $value .= 'W';
            }
        }

        return $value;
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function league()
    {
        return $this->belongsTo(League::class);
    }
}
