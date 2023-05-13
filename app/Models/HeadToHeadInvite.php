<?php

namespace App\Models;

class HeadToHeadInvite extends Base
{
    const STATUS_INVITED = 2;

    protected $fillable = array(
        'user_id',
        'head_to_head_id',
	'is_wager'
    );

    protected $hidden = [
        'status',
        'user_id',
        'created_at',
        'updated_at',
        'head_to_head_id'
    ];

    public function headToHead()
    {
        return $this->belongsTo(HeadToHead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getInfo()
    {
        return [
            'id' => $this->headToHead->id,
            'name' => $this->headToHead->name,
            'date' => $this->headToHead->schedule->date,
            'is_pick' => $this->headToHead->is_pick,
            'league' => $this->headToHead->schedule->league
        ];
    }
}
