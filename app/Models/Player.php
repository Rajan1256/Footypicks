<?php

namespace App\Models;

class Player extends Base
{
    public $timestamps = false;

    protected $fillable = array(
        'position',
        'name',
        'nationality',
        'jersey_number',
        'market_value',
        'contract_until',
        'parse_id',
        'team_id',
    );

    protected $hidden = [
        'team_id',
        'parse_id',
        'nationality',
        'market_value',
        'contract_until',
        'status',
    ];
}
