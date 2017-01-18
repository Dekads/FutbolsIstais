<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $table = 'goals';

    public function game()
    {
    	return $this->hasOne('App\Game', 'id', 'game_id');
    }

        public function player()
    {
    	return $this->hasOne('App\Player', 'id', 'scorer');
    }
}
