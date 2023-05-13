<?php

namespace App;
use App\Models\Feeds;
use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    protected $fillable = [
        'user_id', 'post_id', 'flag_like',
    ];

}
