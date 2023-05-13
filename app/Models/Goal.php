<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $fillable = array(
      'sch_id','team_id','player_id','minute','gol','status'
    );
}
