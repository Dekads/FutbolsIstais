<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $table = 'games';

    public function penalties()
    {
        //return $this->hasOne('App\Team');
        return $this->hasMany('App\Penalty', 'game_id', 'id');
    }

    public function team1()
    {
    	return $this->hasOne('App\Team', 'id', 'team1_id');
    }

    public function team2()
    {
    	return $this->hasOne('App\Team', 'id', 'team2_id');
    }
}
