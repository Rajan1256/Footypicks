<?php

namespace App\Models;
use DB;
class Schedule extends Base
{
    const CANCELED = 0;
    const FINISHED = 1;
    const TIMED = 2;
    const IN_PLAY = 3;
    const POSTPONED = 4;
    const SCHEDULED = 5;
    const PAUSED = 6;
    public $timestamps = false;

    const PLAY_STATUS = [
        0 => 'CANCELED',
        1 => 'FINISHED',
        2 => 'TIMED',
        3 => 'IN_PLAY',
        4 => 'POSTPONED',
        5 => 'SCHEDULED',
	6 => 'PAUSED',
    ];

    protected $fillable = array(
        'team_home_id',
        'team_home_id_parse',
        'team_away_id',
        'team_away_id_parse',
        'date',
        'matchday',
	'schedule_stage',
        'league_id',
	'sch_id',
        'status',
        'goals_home_team',
        'goals_away_team',
        'start_game_time',
    );

    protected $hidden = [
        'team_home_id',
        'team_home_id_parse',
        'team_away_id',
        'team_away_id_parse',
    ];

    public static function prepareStatus($status = '')
    {
        $statuses = array_flip(self::PLAY_STATUS);
        return $statuses[$status] ?? self::CANCELED;
    }


    public function getDateAttribute($data = '')
    {
        return strtotime($data);
    }

    public function getStatusAttribute($status = '')
    {
        return $this::PLAY_STATUS[$status] ?? $this::PLAY_STATUS[$this::CANCELED];
    }

    public function getStatusInt()
    {
        return $this->attributes['status'];
    }

    public static function prepareArrayStatus(array $statusArray = [])
    {
        $result = [];
        foreach ($statusArray as $status) {
            $result[] = self::prepareStatus($status);
        }

        return $result;
    }

    public function prepareStringStatus()
    {
        return $this::PLAY_STATUS[$this->status] ?? $this::PLAY_STATUS[$this::CANCELED];
    }

    public function league()
    {
        return $this->hasOne(League::class, 'id', 'league_id');
    }

    public function teamHome()
    {
        return $this->hasOne(Team::class, 'id', 'team_home_id');
    }

    public function teamAway()
    {
        return $this->hasOne(Team::class, 'id', 'team_away_id');
    }

    public function bet()
    {
        return $this->hasOne(Bet::class, 'schedule_id', 'id');
    }

    public function bets()
    {
        return $this->hasMany(Bet::class, 'schedule_id', 'id');
    }

    public function headToHeads()
    {
        return $this->hasMany(HeadToHead::class, 'schedule_id', 'id');
    }

    public function getResultByTeamId($teamId){
        if($this->goals_home_team == $this->goals_away_team) {
            return Bet::TYPE_DRAW;
        }
        $result = $this->goals_home_team > $this->goals_away_team; //true - h
        $result = ($this->isHomeTeam($teamId)) ? $result : !$result;
        return ($result) ? Bet::TYPE_WIN : Bet::TYPE_LOSE;
    }

    public function getInfoForTeam($teamId)
    {
    	  $log_user = DB::table('teams')
            ->join('leagues','leagues.id','=','teams.league_id')
            ->where('teams.id',$teamId)
            ->first();
        return [
            'id' => $this->id,
            'league_id' => $this->league_id,
            'date' => $this->date,
            'matchday' => $this->matchday,
             'schedule_satge'=>$this->schedule_stage,
            'isCurrentRound'=>$log_user->current_matchday==0?$log_user->match_stage:$log_user->current_matchday,
            'status' => $this->status,
	    'sch_id'=>$this->sch_id,
            'team' => ($this->isHomeTeam($teamId)) ? $this->teamAway : $this->teamHome,
            'goals_home_team' => $this->goals_home_team,
            'goals_away_team' => $this->goals_away_team,
            'is_home' => ($this->team_home_id == $teamId),
        ];
    }

    public function getInfoWithResult()
    {
        return [
            'id' => $this->id,
            'league_id' => $this->league_id,
            'date' => $this->date,
            'matchday' => $this->matchday,
		'schedule_stage'=>$this->schedule_stage,
            'status' => $this->status,
	'sch_id'=>$this->sch_id,
            'team_home' => $this->teamHome,
            'team_away' => $this->teamAway,
            'goals_home_team' => $this->goals_home_team,
            'goals_away_team' => $this->goals_away_team,
            'bet' => $this->bet,
            'is_win' => $this->isWin(),
        ];
    }

    public function isNotPlayed():bool
    {
        $status = $this->getIntStatus();
        return $status == $this::SCHEDULED || $status == $this::IN_PLAY || $status == $this::TIMED;
    }

    public function getIntStatus(){
        return $this->attributes['status'];
    }

    public function isWin():bool
    {
        if(!$this->bet) {
            return false;
        }

        return $this->getResultByTeamId($this->bet->team_id) == $this->bet->type;
    }

    public function isHomeTeam($teamId):bool
    {
        return $this->team_home_id == $teamId;
    }
}
