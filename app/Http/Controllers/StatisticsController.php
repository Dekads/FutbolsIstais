<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use View;

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
use \DB;

class StatisticsController extends Controller
{
    public function table() 
    {
    	$data = Team::orderBy('points', 'DESC')->get();
    	//return view('statistics.table', $data);
    	return View::make('statistics.table', compact('data'));
    }

    public function top10() 
    {
    	//$data = Player::all();


    	//$data = Player::with('goals')->get();


    	/*$data = Player::with('goals', 'passes')->get()->sortBy(function($player)
		{
		    return $player->goals()->count();
		}, 0, true)
		->sortBy(function($player)
		{
		    return $player->passes()->count();
		}, 0, true);*/

		/*$data = Player::with(array('goals' => function($query) {
        	$query->orderBy('result', 'DESC');
    	}))
    	->get();*/


    	$data = Player::leftJoin('passes', 'passes.player_id', '=', 'players.id')
    	->leftJoin('goals', 'goals.scorer', '=', 'players.id')
       	->groupBy('players.id')
       	->orderBy('goals_count', 'DESC')
    	->orderBy('passes_count', 'DESC')
    	->take(10)
    	->get(['players.*', \DB::raw('COUNT(`passes`.`id`) AS `passes_count`'), \DB::raw('COUNT(`goals`.`id`) AS `goals_count`')]);


    	//return view('statistics.table', $data);
    	return View::make('statistics.top10', compact('data'));
    }

    public function penalties()
    {
    	$data = Player::leftJoin('penalties', 'penalties.player_id', '=', 'players.id')
    	->groupBy('players.id')
    	->orderBy('penalties_count', 'DESC')
    	->get(['players.*', \DB::raw('COUNT(`penalties`.`id`) AS `penalties_count`')]);
    	return View::make('statistics.penalties', compact('data'));
    }

    public function team_players($id = null)
    {
    	$data = Player::where('team_id', $id)->get();
    	return View::make('statistics.team_players', compact('data'));
    }

    public function golies_stats()
    {
    	$data = Player::where('position', 'V')->get();
    	return View::make('statistics.golies', compact('data'));
    }

    public function top5_goalies()
    {
    	/*$data = Player::leftJoin('gametime', 'gametime.player_id', '=', 'players.id')
    	->where('players.position', 'V')
    	->where('gametime.time', '>', 0)
    	->groupBy('players.id')
    	//->take(5)
    	->get(['players.*', \DB::raw('COUNT(`gametime`.`id`) AS `games_count`')]);

    	$data2 = Player::leftJoin('goals', 'goals.goalie_id', '=', 'players_id')
    	->groupBy('players.id')
    	->get(['players.*', \DB::raw('COUNT(`gametime`.`id`) AS `games_count`')]);*/
    	
    	DB::enableQueryLog();
    	//$data = Player::where('position', 'V')->get();
    	$data = Player::join('gametime', 'players.id', '=', 'gametime.player_id')
    	
    	->where('gametime.time', '>', 0)
    	->where('players.position', '=', 'V')
    	->groupBy('players.id')

    	->get(['players.*']);

    	//dd(DB::getQueryLog());

    	foreach ($data as $player) {
    		//echo $player->id . " - " .$player->goalie_goals()->count() . " - " . $player->games()->count() . "<br />" ;
    		$player->stats = $player->goalie_goals()->count() / $player->games()->count();
    	}

    	$data = $data->sortBy('stats'/*, SORT_REGULAR, true*/)->take(5);

    	
    	//$data = Player::with('games', 'goalie_goals')->where('position', 'V')->get();
    	
    	

    	/*$data = DB::select('players.*')
    		->addSelect(DB::raw("COUNT(`goals.id.`) AS `gg`"))
    		->join('goals', 'goals.golie_id', '=', 'players.id');*/


    	return View::make('statistics.top5_goalies', compact('data'));

    }

    public function referee_top() 
    {
    	$referees = Referee::all();
    	foreach ($referees as $referee) {
    		$referee->penalties = 0;
    		$referee->games = 0;
    		$games = Game::where('referee1_id', $referee->id)
    			->orWhere('referee2_id', $referee->id)
    			->orWhere('referee3_id', $referee->id)->get();
    		foreach ($games as $game) {
    			$referee->games++;
    			$referee->penalties += $game->penalties()->count();
    		}
    		$referee->stats = $referee->penalties/$referee->games;
    	}
    	$referees = $referees->sortBy('stats', SORT_REGULAR, true);
    	
    	return View::make('statistics.top_referees', compact('referees'));
    }

    public function longest_games()
    {
    	$time = Goal::selectRaw('*, MAX(time) as longest')->groupBy('game_id')->orderBy('longest', 'DESC')->take(5)->get();
    	return View::make('statistics.longest_games', compact('time'));
    }

    public function fastest_goals()
    {
    	$time = Goal::select('*')->orderBy('time', 'ASC')->take(5)->get();
    	return View::make('statistics.fastest_goals', compact('time'));
	}
}


/*
$data = Player::leftJoin('gametime', 'gametime.player_id', '=', 'players.id')
    	->leftJoin('goals', 'goals.goalie_id', '=', 'players.id')
    	->where('players.position', 'V')
    	->where('gametime.time', '>', 0)
    	->groupBy('players.id')


    	//->take(5)
    	->get(['players.*', \DB::raw('COUNT(`gametime`.`id`) AS `games_count`'), \DB::raw('COUNT(`goals`.`id`) AS `goals_count`')]);
    	
*/