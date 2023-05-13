<?php

namespace App\Models;
namespace App\Models;

class PushToken extends Base
{
    protected $fillable = array(
        'token',
        'user_id',
        'device_type'
    );

    protected $hidden = [
        'updated_at',
        'user_id'
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
