<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Storage;
class PostComment extends Model
{

    protected $fillable = [
        'user_id', 'post_id', 'u_comment',
    ];


    public function user()
    {
        return $this->belongsTo(Models\User::class);
    }


}
