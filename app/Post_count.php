<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post_count extends Model
{
    protected $fillable = [
        'post_id','o_user_id', 'is_read',
    ];

}
