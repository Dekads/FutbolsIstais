<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $table = 'players';

    public function goals()
    {
        //return $this->hasMany('App\Goal');
        return $this->hasMany('App\Goal', 'scorer', 'id');
    }

    public function passes()
    {
        //return $this->hasMany('App\Pass');
        return $this->hasMany('App\Pass', 'player_id', 'id');
    }

    public function team()
    {
        //return $this->hasOne('App\Team');
        return $this->hasOne('App\Team', 'id', 'team_id');
    }

    public function penalties()
    {
        //return $this->hasOne('App\Team');
        return $this->hasOne('App\Penalty', 'player_id', 'id');
    }

    public function penalty_yellow()
    {
        //return $this->hasOne('App\Team');
        return $this->hasOne('App\Penalty', 'player_id', 'id')->where('red', 0);
    }

    public function penalty_red()
    {
        //return $this->hasOne('App\Team');
        return $this->hasOne('App\Penalty', 'player_id', 'id')->where('red', 1);
    }

    public function games()
    {
        //return $this->hasOne('App\Team');
        return $this->hasMany('App\GameTime', 'player_id', 'id')->where('time', '>', 0);
    }

    public function goalie_goals()
    {
    	return $this->hasMany('App\Goal', 'goalie_id', 'id');
    }

    public function games_pamats()
    {
        //return $this->hasOne('App\Team');
        return $this->hasMany('App\GameTime', 'player_id', 'id')->where('time', '>', 0)->where('pamats', '1');
    }

    public function played_time()
    {
    	return $this->hasMany('App\GameTime', 'player_id', 'id')
       		->selectRaw('player_id, sum(time) as onfield')
       		->groupBy('player_id');
    }
}
