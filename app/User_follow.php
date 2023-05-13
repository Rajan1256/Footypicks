<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_follow extends Model
{
    protected $fillable = [
        'user_id', 'follow_id', 'flag_follow',
    ];
}
