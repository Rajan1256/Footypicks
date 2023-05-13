<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_Feed_Count extends Model
{
    protected $fillable = [
        'user_id','post_id', 'post_user_id', 'like_user_id','follow_user_id','is_read'
    ];
}
