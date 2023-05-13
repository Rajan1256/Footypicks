<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;

class NotificationModel extends Base
{
    const TYPE_GLOBAL = 0;

    const TYPE_GAME_INVITED = 1;
    const TYPE_GAME_WIN = 2;
    const TYPE_GAME_LOSE = 3;
    const TYPE_GAME_TIE = 4;

    const TYPE_HTH_INVITED = 5;
    const TYPE_HTH_WIN = 6;
    const TYPE_HTH_LOSE = 7;
    const TYPE_HTH_TIE = 8;

    const TYPE_DARE_INVITED = 9;
    const TYPE_DARE_WIN = 10;
    const TYPE_DARE_LOSE = 11;
    const TYPE_DARE_TIE = 12;

    const TYPE_CREATE_GAME = 13;
    const TYPE_CREATE_HTH = 14;
    const TYPE_CREATE_DARE = 15;

    protected $table = 'notifications';

    public $timestamps = false;

    protected $fillable = array(
        'message',
        'created_at',
    );

    protected $hidden = [
        'id',
        'user_id',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
    
    public function getCreatedAtAttribute($data = '')
    {
        return strtotime($data);
    }
}
