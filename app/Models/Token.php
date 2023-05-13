<?php

namespace App\Models;

use Laravel\Passport\Token as BaseToken;

class Token extends BaseToken
{
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];



    /**
     * Get the user that the token belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {

        return $this->belongsTo(User::class);
    }
}
