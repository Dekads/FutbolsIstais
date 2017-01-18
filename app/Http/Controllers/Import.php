<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Team;
use App\Referee;
use App\Game;
use App\Player;
use App\Penalty;
use App\Goal;
use App\PlayerSwitch;
use App\GameTime;
use App\Pass;
use File;
use Session;
use Redirect;

class Import extends Controller
{

	private function theTime($time)
	{
		$part = explode(":", $time);
		return (($part[0]*60)+$part[1]);

	}

    public function json($file)
    {

    	$lastGoal = 0;
    	$g1 = 0;
    	$g2 = 0;

    	$t1_gk = array();
    	$t2_gk = array();

    	//$data = file_get_contents("http://46.19.144.51/futbols2.json");
    	$data = File::get(storage_path().'/uploads/'.$file.'.json');
		$json = json_decode($data);

		$team1 = Team::where('name', $json->Spele->Komanda[0]->Nosaukums)->first();

		if (!$team1)
		{ 
			$team1 = new Team;
       	 	$team1->name = $json->Spele->Komanda[0]->Nosaukums;
        	$team1->save();
		}



		


		$team2 = Team::where('name', $json->Spele->Komanda[1]->Nosaukums)->first();

		if (!$team2)
		{ 
			$team2 = new Team;
       	 	$team2->name = $json->Spele->Komanda[1]->Nosaukums;
        	$team2->save();
		}



		$r1 = Referee::where('name', $json->Spele->VT->Vards)->where('surname', $json->Spele->VT->Uzvards)->first();

		$r2 = Referee::where('name', $json->Spele->T[0]->Vards)->where('surname', $json->Spele->T[0]->Uzvards)->first();

		$r3 = Referee::where('name', $json->Spele->T[1]->Vards)->where('surname', $json->Spele->T[1]->Uzvards)->first();

		if (!$r1)
		{ 
			$r1 = new Referee;
       	 	$r1->name = $json->Spele->VT->Vards;
       	 	$r1->surname = $json->Spele->VT->Uzvards;
        	$r1->save();
		}

		if (!$r2)
		{ 
			$r2 = new Referee;
       	 	$r2->name = $json->Spele->T[0]->Vards;
       	 	$r2->surname = $json->Spele->T[0]->Uzvards;
        	$r2->save();
		}

		if (!$r3)
		{ 
			$r3 = new Referee;
       	 	$r3->name = $json->Spele->T[1]->Vards;
       	 	$r3->surname = $json->Spele->T[1]->Uzvards;
        	$r3->save();
		}

		/*	
			date
			location
			viewers
			team1_id
			team2_id
			referee1_id
			referee2_id
			referee3_id
		*/

		$game = Game::where('date', $json->Spele->Laiks)->where('team1_id', $team1->id)->where('team2_id', $team2->id)->first();

		if(!$game) {
			$game = new Game;
	   	 	$game->date = $json->Spele->Laiks;
	   	 	$game->location = $json->Spele->Vieta;
	   	 	$game->viewers = $json->Spele->Skatitaji;
	   	 	$game->team1_id = $team1->id;
	   	 	$game->team2_id = $team2->id;
	   	 	$game->referee1_id = $r1->id;
	   	 	$game->referee2_id = $r2->id;
	   	 	$game->referee3_id = $r3->id;
	    	$game->save();
		}
		else
		{
			Session::flash('error', 'This game already exists!');
			return Redirect::back();
			//exit("Šāda spēle jau ir pievienota");
		}

		// TEAM1

		/*
			name
			surname
			number
			position
			team_id
		*/

		foreach($json->Spele->Komanda[0]->Speletaji->Speletajs as $speletajs)
		{
			$player = Player::where('number', $speletajs->Nr)->where('team_id', $team1->id)->first();

			if (!$player)
			{ 
				$player = new Player;
	       	 	$player->name = $speletajs->Vards;
	       	 	$player->surname = $speletajs->Uzvards;
	       	 	$player->number = $speletajs->Nr;
	       	 	$player->position = $speletajs->Loma;
	       	 	$player->team_id = $team1->id;
	        	$player->save();
			}

			$gametime = new GameTime;
			$gametime->game_id = $game->id;
			$gametime->team_id = $team1->id;
			$gametime->player_id = $player->id;
			$gametime->start = 0;
			$gametime->time = 0;
			$gametime->save();
		}

		foreach($json->Spele->Komanda[0]->Pamatsastavs->Speletajs as $speletajs)
		{
			$player = Player::where('number', $speletajs->Nr)->where('team_id', $team1->id)->first();
			$field = GameTime::where('player_id', $player->id)->where('game_id', $game->id)->update(array('field' => 1, 'pamats' => 1));
			if($player->position == 'V') $t1_gk[] = array($player->id, 0, 2147483647);
		}


		if(isset($json->Spele->Komanda[0]->Sodi->Sods)) 
		{

			if(isset($json->Spele->Komanda[0]->Sodi->Sods->Laiks))
			{

				$penalty = new Penalty;
				$player = Player::where('number', $json->Spele->Komanda[0]->Sodi->Sods->Nr)->where('team_id', $team1->id)->first();

				if(Penalty::where('player_id', $player->id)->where('game_id', $game->id)->count()==1) {
					$penaly->red = 1;
					$gametime = GameTime::where('game_id', $game->id)->where('player_id', $player->id)->first();
					$field = GameTime::where('player_id', $player->id)->where('game_id', $game->id)->update(array('field' => 0, 'time' => ($gametime->time + ($this->theTime($json->Spele->Komanda[0]->Sodi->Sods->Laiks)) - $gametime->start)));
				}
				$penalty->game_id = $game->id;
				$penalty->team_id = $team1->id;
				$penalty->player_id = $player->id;
				$penalty->time = $this->theTime($json->Spele->Komanda[0]->Sodi->Sods->Laiks);
				$penalty->save();

			}
			else
			{
				foreach($json->Spele->Komanda[0]->Sodi->Sods as $sods)
				{
					$penalty = new Penalty;
					$player = Player::where('number', $sods->Nr)->where('team_id', $team1->id)->first();
					if(Penalty::where('player_id', $player->id)->where('game_id', $game->id)->count()==1) {
						$penalty->red = 1;
						$gametime = GameTime::where('game_id', $game->id)->where('player_id', $player->id)->first();
						$field = GameTime::where('player_id', $player->id)->where('game_id', $game->id)->update(array('field' => 0, 'time' => ($gametime->time + ($sods->Laiks) - $gametime->start)));
					}
					$penalty->game_id = $game->id;
					$penalty->team_id = $team1->id;
					$penalty->player_id = $player->id;
					$penalty->time = $this->theTime($sods->Laiks);
					$penalty->save();
				}
			}
			
		}

		if(isset($json->Spele->Komanda[0]->Mainas->Maina)) 
		{
			if(isset($json->Spele->Komanda[0]->Mainas->Maina->Laiks))
			{
				$player1 = Player::where('number', $json->Spele->Komanda[0]->Mainas->Maina->Nr1)->where('team_id', $team1->id)->first(); //iet nost
				$player2 = Player::where('number', $json->Spele->Komanda[0]->Mainas->Maina->Nr2)->where('team_id', $team1->id)->first(); //nak virsu
			
				foreach ($t1_gk as $vartsargs) {
					if($vartsargs[0]==$player1->id && $vartsargs[2]==2147483647) $vartsargs[2] = $this->theTime($json->Spele->Komanda[0]->Mainas->Maina->Laiks);
				}
				if($player2->position == 'V') $t1_gk[] = array($player2->id, $this->theTime($json->Spele->Komanda[0]->Mainas->Maina->Laiks), 2147483647);	

				$switch = new PlayerSwitch;
				$switch->team_id = $team1->id;
				$switch->game_id = $game->id;
				$switch->player1_id = $player1->id;
				$switch->player2_id = $player2->id;
				$switch->time = $this->theTime($json->Spele->Komanda[0]->Mainas->Maina->Laiks);
				$switch->save();

				$gametime = GameTime::where('team_id', $team1->id)->where('game_id', $game->id)->where('player_id', $player1->id)->first();
				$gametime2 = GameTime::where('team_id', $team1->id)->where('game_id', $game->id)->where('player_id', $player1->id)->update(array('field' => 0, 'time' => ($gametime->time + ($this->theTime($json->Spele->Komanda[0]->Mainas->Maina->Laiks) - $gametime->start))));

				$gametime = GameTime::where('team_id', $team1->id)->where('game_id', $game->id)->where('player_id', $player2->id)->first();
				$gametime2 = GameTime::where('team_id', $team1->id)->where('game_id', $game->id)->where('player_id', $player2->id)->update(array('field' => 1, 'start' => $this->theTime($json->Spele->Komanda[0]->Mainas->Maina->Laiks)));
				

			}
			else
			{


				foreach($json->Spele->Komanda[0]->Mainas->Maina as $maina)
				{
					$player1 = Player::where('number', $maina->Nr1)->where('team_id', $team1->id)->first(); //iet nost
					$player2 = Player::where('number', $maina->Nr2)->where('team_id', $team1->id)->first(); //nak virsu
					foreach ($t1_gk as $vartsargs) {
						if($vartsargs[0]==$player1->id && $vartsargs[2]==2147483647) $vartsargs[2] = $this->theTime($maina->Laiks);
					}
					if($player2->position == 'V') $t1_gk[] = array($player2->id, $this->theTime($maina->Laiks), 2147483647);

					$switch = new PlayerSwitch;
					$switch->team_id = $team1->id;
					$switch->game_id = $game->id;
					$switch->player1_id = $player1->id;
					$switch->player2_id = $player2->id;
					$switch->time = $this->theTime($maina->Laiks);
					$switch->save();

					$gametime = GameTime::where('team_id', $team1->id)->where('game_id', $game->id)->where('player_id', $player1->id)->first();
					$gametime2 = GameTime::where('team_id', $team1->id)->where('game_id', $game->id)->where('player_id', $player1->id)->update(array('field' => 0, 'time' => ($gametime->time + ($this->theTime($maina->Laiks) - $gametime->start))));

					$gametime = GameTime::where('team_id', $team1->id)->where('game_id', $game->id)->where('player_id', $player2->id)->first();
					$gametime2 = GameTime::where('team_id', $team1->id)->where('game_id', $game->id)->where('player_id', $player2->id)->update(array('field' => 1, 'start' => $this->theTime($maina->Laiks)));
					
				}

			}


		
		}

		/*
		"Varti": {"VG": [
	    {
	     "Laiks": "06:09",
	     "P": [
	      {"Nr": 96},
	      {"Nr": 55}
	     ],
	     "Nr": 24,
	     "Sitiens": "N"
	    },
	    {
	     "Laiks": "11:07",
	     "P": [
	      {"Nr": 24},
	      {"Nr": 37},
	      {"Nr": 16}
	     ],
	     "Nr": 73,
	     "Sitiens": "N"
	    }
	   	]},
	    */





		// TEAM2

		/*
			name
			surname
			number
			position
			team_id
		*/

		foreach($json->Spele->Komanda[1]->Speletaji->Speletajs as $speletajs)
		{
			$player = Player::where('number', $speletajs->Nr)->where('team_id', $team2->id)->first();

			if (!$player)
			{ 
				$player = new Player;
	       	 	$player->name = $speletajs->Vards;
	       	 	$player->surname = $speletajs->Uzvards;
	       	 	$player->number = $speletajs->Nr;
	       	 	$player->position = $speletajs->Loma;
	       	 	$player->team_id = $team2->id;
	        	$player->save();
			}

			$gametime = new GameTime;
			$gametime->game_id = $game->id;
			$gametime->team_id = $team2->id;
			$gametime->player_id = $player->id;
			$gametime->start = 0;
			$gametime->time = 0;
			$gametime->save();
		}

		foreach($json->Spele->Komanda[1]->Pamatsastavs->Speletajs as $speletajs)
		{
			$player = Player::where('number', $speletajs->Nr)->where('team_id', $team2->id)->first();
			$field = GameTime::where('player_id', $player->id)->where('game_id', $game->id)->update(array('field' => 1, 'pamats' => 1));
			if($player->position == 'V') $t2_gk[] = array($player->id, 0, 2147483647);
		}

		if(isset($json->Spele->Komanda[1]->Sodi->Sods)) 
		{

			if(isset($json->Spele->Komanda[1]->Sodi->Sods->Laiks))
			{
				$penalty = new Penalty;
				$player = Player::where('number', $json->Spele->Komanda[1]->Sodi->Sods->Nr)->where('team_id', $team2->id)->first();

				if(Penalty::where('player_id', $player->id)->where('game_id', $game->id)->count()==1) {
					$penaly->red = 1;
					$gametime = GameTime::where('game_id', $game->id)->where('player_id', $player->id)->first();
					$field = GameTime::where('player_id', $player->id)->where('game_id', $game->id)->update(array('field' => 0, 'time' => ($gametime->time + ($this->theTime($json->Spele->Komanda[1]->Sodi->Sods->Laiks)) - $gametime->start)));
				}
				$penalty->game_id = $game->id;
				$penalty->team_id = $team2->id;
				$penalty->player_id = $player->id;
				$penalty->time = $this->theTime($json->Spele->Komanda[1]->Sodi->Sods->Laiks);
				$penalty->save();
			}
			else
			{
				foreach($json->Spele->Komanda[1]->Sodi->Sods as $sods)
				{
					
					$penalty = new Penalty;
					$player = Player::where('number', $sods->Nr)->where('team_id', $team2->id)->first();
					if(Penalty::where('player_id', $player->id)->where('game_id', $game->id)->count()==1) {
						$penalty->red = 1;
						$gametime = GameTime::where('game_id', $game->id)->where('player_id', $player->id)->first();
						$field = GameTime::where('player_id', $player->id)->where('game_id', $game->id)->update(array('field' => 0, 'time' => ($gametime->time + ($this->theTime($sods->Laiks)) - $gametime->start)));
					}
					$penalty->game_id = $game->id;
					$penalty->team_id = $team2->id;
					$penalty->player_id = $player->id;
					$penalty->time = $this->theTime($sods->Laiks);
					$penalty->save();
				}
			}
			
		}

		if(isset($json->Spele->Komanda[1]->Mainas->Maina)) 
		{

			if(isset($json->Spele->Komanda[1]->Mainas->Maina->Laiks))
			{
				$player1 = Player::where('number', $json->Spele->Komanda[1]->Mainas->Maina->Nr1)->where('team_id', $team2->id)->first(); //iet nost
				$player2 = Player::where('number', $json->Spele->Komanda[1]->Mainas->Maina->Nr2)->where('team_id', $team2->id)->first(); //nak virsu
				foreach ($t2_gk as $vartsargs) {
					if($vartsargs[0]==$player1->id && $vartsargs[2]==2147483647) $vartsargs[2] = $this->theTime($json->Spele->Komanda[1]->Mainas->Maina->Laiks);
				}
				if($player2->position == 'V') $t2_gk[] = array($player2->id, $this->theTime($json->Spele->Komanda[1]->Mainas->Maina->Laiks), 2147483647);

				$switch = new PlayerSwitch;
				$switch->team_id = $team2->id;
				$switch->game_id = $game->id;
				$switch->player1_id = $player1->id;
				$switch->player2_id = $player2->id;
				$switch->time = $this->theTime($json->Spele->Komanda[1]->Mainas->Maina->Laiks);
				$switch->save();

				$gametime = GameTime::where('game_id', $game->id)->where('player_id', $player1->id)->first();
				$gametime2 = GameTime::where('game_id', $game->id)->where('player_id', $player1->id)->update(array('field' => 0, 'time' => ($gametime->time + ($this->theTime($json->Spele->Komanda[1]->Mainas->Maina->Laiks) - $gametime->start))));

				$gametime = GameTime::where('game_id', $game->id)->where('player_id', $player2->id)->first();
				$gametime2 = GameTime::where('game_id', $game->id)->where('player_id', $player2->id)->update(array('field' => 1, 'start' => $this->theTime($json->Spele->Komanda[1]->Mainas->Maina->Laiks)));

			}
			else
			{


				foreach($json->Spele->Komanda[1]->Mainas->Maina as $maina)
				{
					$player1 = Player::where('number', $maina->Nr1)->where('team_id', $team2->id)->first(); //iet nost
					$player2 = Player::where('number', $maina->Nr2)->where('team_id', $team2->id)->first(); //nak virsu
					foreach ($t2_gk as $vartsargs) {
						if($vartsargs[0]==$player1->id && $vartsargs[2]==2147483647) $vartsargs[2] = $this->theTime($maina->Laiks);
					}
					if($player2->position == 'V') $t2_gk[] = array($player2->id, $this->theTime($maina->Laiks), 2147483647);

					$switch = new PlayerSwitch;
					$switch->team_id = $team2->id;
					$switch->game_id = $game->id;
					$switch->player1_id = $player1->id;
					$switch->player2_id = $player2->id;
					$switch->time = $this->theTime($maina->Laiks);
					$switch->save();

					$gametime = GameTime::where('game_id', $game->id)->where('player_id', $player1->id)->first();
					$gametime2 = GameTime::where('game_id', $game->id)->where('player_id', $player1->id)->update(array('field' => 0, 'time' => ($gametime->time + ($this->theTime($maina->Laiks) - $gametime->start))));

					$gametime = GameTime::where('game_id', $game->id)->where('player_id', $player2->id)->first();
					$gametime2 = GameTime::where('game_id', $game->id)->where('player_id', $player2->id)->update(array('field' => 1, 'start' => $this->theTime($maina->Laiks)));
				}

			}

		}

		/*
		"Varti": {"VG": [
	    {
	     "Laiks": "06:09",
	     "P": [
	      {"Nr": 96},
	      {"Nr": 55}
	     ],
	     "Nr": 24,
	     "Sitiens": "N"
	    },
	    {
	     "Laiks": "11:07",
	     "P": [
	      {"Nr": 24},
	      {"Nr": 37},
	      {"Nr": 16}
	     ],
	     "Nr": 73,
	     "Sitiens": "N"
	    }
	   	]},
	    */
		if(isset($json->Spele->Komanda[1]->Varti->VG)) 
		{

			if(isset($json->Spele->Komanda[1]->Varti->VG->Laiks))
			{
				$g2++;
				$goal = new Goal;
				$player = Player::where('number', $json->Spele->Komanda[1]->Varti->VG->Nr)->where('team_id', $team2->id)->first();
				foreach ($t1_gk as $vartsargs) {
					if($vartsargs[1] < $this->theTime($json->Spele->Komanda[1]->Varti->VG->Laiks) && $vartsargs[2] > $this->theTime($json->Spele->Komanda[1]->Varti->VG->Laiks)) {
						$goal->goalie_id = $vartsargs[0];
						break;
					}
				}
				$goal->scorer = $player->id;
				$goal->time = $this->theTime($json->Spele->Komanda[1]->Varti->VG->Laiks);
				$goal->game_id = $game->id;
				$goal->team_id = $team2->id;
				$goal->type = $json->Spele->Komanda[1]->Varti->VG->Sitiens;
				$goal->save();

				

				if($this->theTime($json->Spele->Komanda[1]->Varti->VG->Laiks) > $lastGoal) 
					$lastGoal = $this->theTime($json->Spele->Komanda[1]->Varti->VG->Laiks);

				//Passes
				if(isset($json->Spele->Komanda[1]->Varti->VG->P->Nr))
				{
					$player = Player::where('number', $json->Spele->Komanda[1]->Varti->VG->P->Nr)->where('team_id', $team2->id)->first();
					$pass = new Pass;
					$pass->player_id = $player->id;
					$pass->goal_id = $goal->id;
					$pass->save();
				}
				else
				{
					if(isset($json->Spele->Komanda[1]->Varti->VG->P))
					{
						foreach($json->Spele->Komanda[1]->Varti->VG->P as $passer)
						{
							$player = Player::where('number', $passer->Nr)->where('team_id', $team2->id)->first();
							$pass = new Pass;
							$pass->player_id = $player->id;
							$pass->goal_id = $goal->id;
							$pass->save();
						}
					}
					
				}
			}
			else
			{

				foreach($json->Spele->Komanda[1]->Varti->VG as $VG)
				{
					$g2++;
					$goal = new Goal;
					$player = Player::where('number', $VG->Nr)->where('team_id', $team2->id)->first();
					foreach ($t1_gk as $vartsargs) {
						if($vartsargs[1] < $this->theTime($VG->Laiks) && $vartsargs[2] > $this->theTime($VG->Laiks)) {
							$goal->goalie_id = $vartsargs[0];
							break;
						}
					}
					$goal->scorer = $player->id;
					$goal->time = $this->theTime($VG->Laiks);
					$goal->game_id = $game->id;
					$goal->team_id = $team2->id;
					$goal->type = $VG->Sitiens;
					$goal->save();

					

					if($this->theTime($VG->Laiks) > $lastGoal) 
						$lastGoal = $this->theTime($VG->Laiks);

					//Passes
					if(isset($VG->P->Nr))
					{
						$player = Player::where('number', $VG->P->Nr)->where('team_id', $team2->id)->first();
						$pass = new Pass;
						$pass->player_id = $player->id;
						$pass->goal_id = $goal->id;
						$pass->save();
					}
					else
					{
						if(isset($VG->P))
						{
							foreach($VG->P as $passer)
							{
								$player = Player::where('number', $passer->Nr)->where('team_id', $team2->id)->first();
								$pass = new Pass;
								$pass->player_id = $player->id;
								$pass->goal_id = $goal->id;
								$pass->save();
							}
						}
						
					}
				}
			}
		}


		if(isset($json->Spele->Komanda[0]->Varti->VG)) 
		{
			if(isset($json->Spele->Komanda[0]->Varti->VG->Laiks))
			{

				$g1++;
				$goal = new Goal;
				$player = Player::where('number', $json->Spele->Komanda[0]->Varti->VG->Nr)->where('team_id', $team1->id)->first();
				foreach ($t2_gk as $vartsargs) {
					if($vartsargs[1] < $this->theTime($json->Spele->Komanda[0]->Varti->VG->Laiks) && $vartsargs[2] > $this->theTime($json->Spele->Komanda[0]->Varti->VG->Laiks)) {
						$goal->goalie_id = $vartsargs[0];
						break;
					}
				}
				$goal->scorer = $player->id;
				$goal->game_id = $game->id;
				$goal->team_id = $team1->id;
				$goal->time = $this->theTime($json->Spele->Komanda[0]->Varti->VG->Laiks);
				$goal->type = $json->Spele->Komanda[0]->Varti->VG->Sitiens;
				$goal->save();

				

				if($this->theTime($json->Spele->Komanda[0]->Varti->VG->Laiks) > $lastGoal)
					$lastGoal = $this->theTime($json->Spele->Komanda[0]->Varti->VG->Laiks);

				//Passes
				if(isset($json->Spele->Komanda[0]->Varti->VG->P->Nr))
				{
					$player = Player::where('number', $json->Spele->Komanda[0]->Varti->VG->P->Nr)->where('team_id', $team1->id)->first();
					$pass = new Pass;
					$pass->player_id = $player->id;
					$pass->goal_id = $goal->id;
					$pass->save();
				}
				else
				{
					if(isset($json->Spele->Komanda[0]->Varti->VG->P))
					{
						foreach($json->Spele->Komanda[0]->Varti->VG->P as $passer)
						{
							$player = Player::where('number', $passer->Nr)->where('team_id', $team1->id)->first();
							$pass = new Pass;
							$pass->player_id = $player->id;
							$pass->goal_id = $goal->id;
							$pass->save();
						}
					}
					
				}

			}
			else
			{
				foreach($json->Spele->Komanda[0]->Varti->VG as $VG)
				{
					$g1++;
					$goal = new Goal;
					$player = Player::where('number', $VG->Nr)->where('team_id', $team1->id)->first();
					foreach ($t2_gk as $vartsargs) {
						if($vartsargs[1] < $this->theTime($VG->Laiks) && $vartsargs[2] > $this->theTime($VG->Laiks)) {
							$goal->goalie_id = $vartsargs[0];
							break;
						}
					}
					$goal->scorer = $player->id;
					$goal->time = $this->theTime($VG->Laiks);
					$goal->game_id = $game->id;
					$goal->team_id = $team1->id;
					$goal->type = $VG->Sitiens;
					$goal->save();

					

					if($this->theTime($VG->Laiks) > $lastGoal)
						$lastGoal = $this->theTime($VG->Laiks);

					//Passes
					if(isset($VG->P->Nr))
					{
						$player = Player::where('number', $VG->P->Nr)->where('team_id', $team1->id)->first();
						$pass = new Pass;
						$pass->player_id = $player->id;
						$pass->goal_id = $goal->id;
						$pass->save();
					}
					else
					{
						if(isset($VG->P))
						{
							foreach($VG->P as $passer)
							{
								$player = Player::where('number', $passer->Nr)->where('team_id', $team1->id)->first();
								$pass = new Pass;
								$pass->player_id = $player->id;
								$pass->goal_id = $goal->id;
								$pass->save();
							}
						}
						
					}


					
				}

			}


				
		}

		if($lastGoal < 3600)
			$lastGoal = 3600;

		//Game ended. Update played time.
		$gametime = GameTime::where('field', 1)->where('game_id', $game->id)->get();
		foreach ($gametime as $player) {
			$gtime = GameTime::find($player->id);
			$gtime->time = ($player->time + ($lastGoal - $player->start));
			$gtime->save();
		}

		//w=5
		//wot=3
		//lot=2
		//l=1

		$updateTeam1 = Team::find($team1->id);
		$updateTeam2 = Team::find($team2->id);

		if($lastGoal>3600) //varti papildlaika
		{
			if($g1>$g2){
				$updateTeam1->points = $team1->points+3;
				$updateTeam1->games = $team1->games+1;
				$updateTeam1->wot = $team1->wot+1;
				
				$updateTeam2->points = $team2->points+2;
				$updateTeam2->games = $team2->games+1;
				$updateTeam2->wot = $team2->lot+1;
			}else{
				$updateTeam1->points = $team1->points+2;
				$updateTeam1->games = $team1->games+1;
				$updateTeam1->lot = $team1->lot+1;
				
				$updateTeam2->points = $team2->points+3;
				$updateTeam2->games = $team2->games+1;
				$updateTeam2->wot = $team2->wot+1;
			}
		}
		else
		{
			if($g1>$g2){
				$updateTeam1->points = $team1->points+5;
				$updateTeam1->games = $team1->games+1;
				$updateTeam1->w = $team1->w+1;
				
				$updateTeam2->points = $team2->points+1;
				$updateTeam2->games = $team2->games+1;
				$updateTeam2->l = $team2->l+1;
			}else{
				$updateTeam1->points = $team1->points+1;
				$updateTeam1->games = $team1->games+1;
				$updateTeam1->l = $team1->l+1;
				
				$updateTeam2->points = $team2->points+5;
				$updateTeam2->games = $team2->games+1;
				$updateTeam2->w = $team2->w+1;
			}
		}

		//goals
		$updateTeam1->goalsplus = $team1->goalsplus+$g1;
		$updateTeam1->goalsminus = $team1->goalsminus+$g2;

		$updateTeam2->goalsplus = $team2->goalsplus+$g2;
		$updateTeam2->goalsminus = $team2->goalsminus+$g1;

		$updateTeam1->save();
		$updateTeam2->save();

		Session::flash('success', 'Game succesfully imported!');
		return Redirect::back();


    }
}
