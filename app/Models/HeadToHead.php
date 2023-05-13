<?php

namespace App\Models;

class HeadToHead extends Base
{
    const BET_STATUS_PICK = 1;
    const BET_STATUS_SCORE = 0;

    const GAME_TYPE_SINGLE = 1;
    const GAME_TYPE_DARE = 2;

    const TYPE_LOSE = 0;
    const TYPE_DRAW = 1;
    const TYPE_WIN = 2;
    const TYPE_SCORE = 3;

    const STATUS_INVITED = 2;
    const STATUS_FINISH = 5;

    protected $fillable = array(
        'user_id',
        'win_user_id',
        'schedule_id',
        'wish',
        'name',
        'status',
	'wager_amount',
	 'is_wager',
	'wager_currency',
        'is_pick',
        'game_type',
        'win_user_id'
    );

    protected $hidden = [
        'user_id',
        'status',
        'schedule_id',
        'updated_at',
        'game_type',
    ];

    protected $appends = [
        'is_finish'
    ];

    public function getIsPickAttribute($isPick) {
        return (bool) $isPick;
    }

    public function getIsFinishAttribute() {
        return $this->status == $this::STATUS_FINISH;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function winUser()
    {
        return $this->belongsTo(User::class, 'win_user_id', 'id');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function bet()
    {
        return $this->hasOne(HeadToHeadBet::class);
    }

    public function bets()
    {
        return $this->hasMany(HeadToHeadBet::class);
    }

    public function invite()
    {
        return $this->hasOne(HeadToHeadInvite::class);
    }

    public function getInfo()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_pick' => $this->is_pick,
            'win_user_id' => $this->win_user_id,
            'date' => $this->schedule->date,
            'league' => $this->schedule->league,
        ];
    }

    public function getFullInfo($userId)
    {
        return [
            'id' => $this->id,
            'win_user' => $this->win_user,
            'win_user_id' => $this->win_user_id,
            'wish' => $this->wish,
            'created_at' => $this->created_at->timestamp,
            'name' => $this->name,
            'is_pick' => $this->is_pick,
            'is_finish' => $this->is_finish,
            'user' => $this->user,
            'schedule' => $this->schedule,
            'bet' => $this->getUserBet($userId),
			   
        ];
    }

    public function getInfoWithOpponentBet($userId)
    {
	      $dt_cnt = HeadToHeadBet::orderBy("created_at", "desc")->where('head_to_head_id',isset($this->getOpponentBet($userId)->head_to_head_id)?$this->getOpponentBet($userId)->head_to_head_id:0)->count();
        $dt = HeadToHeadBet::orderBy("created_at", "desc")->where('head_to_head_id',isset($this->getOpponentBet($userId)->head_to_head_id)?$this->getOpponentBet($userId)->head_to_head_id:0)->first();

        if($dt_cnt==2)
        {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'is_pick' => $this->is_pick,
                'win_user_id' => $this->win_user_id,
                'date' => $this->schedule->date,
                'league' => $this->schedule->league,
                'opponent_bet' => $this->getOpponentBet($userId),
                'bet' => $this->getUserBet($userId),
                'is_accept'=>$dt->user_id==$userId?'true':'false',
            ];
        }
        else
        {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'is_pick' => $this->is_pick,
                'win_user_id' => $this->win_user_id,
                'date' => $this->schedule->date,
                'league' => $this->schedule->league,
                'opponent_bet' => $this->getOpponentBet($userId),
                'bet' => $this->getUserBet($userId),
                'is_accept'=>'',
            ];
        }         
    }

    public function getOpponentBet($userId){
        $opponentBet = [];
        if(isset($this->bets[1])) {
            $opponentBet = ($this->bets[0]->user_id == $userId) ? $this->bets[1] : $this->bets[0];
        }
        return (object) $opponentBet;
    }

    public function getUserBet($userId){
        $opponentBet = [];
        if(!isset($this->bets[1]) && $this->bets[0]->user_id == $userId) {
            return $this->bets[0];
        }
        
        if(isset($this->bets[0], $this->bets[1])) {
            $opponentBet = ($this->bets[0]->user_id == $userId) ? $this->bets[0] : $this->bets[1];
        }
        return (object) $opponentBet;
    }

    private function getNameOfEntity()
    {
        return ($this->game_type == $this::GAME_TYPE_SINGLE) ? 'HEAD2HEAD game' : 'DARE game';
    }

    public function prepareNotificationMessageCreate()
    {
        return 'You created a new "' . $this->name . '" ' . $this->getNameOfEntity();
    }

    public function prepareNotificationMessageWin()
    {
        return 'You win "' . $this->name . '" ' . $this->getNameOfEntity();
    }

    public function prepareNotificationMessageLose()
    {
        return 'You lose "' . $this->name . '" ' . $this->getNameOfEntity();
    }

    public function prepareNotificationMessageTie()
    {
        return  $this->getNameOfEntity() . ' "' . $this->name . '" is tie';
    }

    public function prepareInviteNotificationMessage()
    {
	 if($this->game_type==1)
        {
            return  'You are invited to "' . $this->name . '" HEAD2HEAD game';
        }
        else
        {
            return  'You are invited to "' . $this->name . '" DARE game';
        }       
    }
}
