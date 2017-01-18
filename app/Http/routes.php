<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


Route::get('statistics/table', [
    'as' => 'table', 'uses' => 'StatisticsController@table'
]);
Route::get('statistics/top10', [
    'as' => 'top10', 'uses' => 'StatisticsController@top10'
]);
Route::get('statistics/penalties', [
    'as' => 'penalties', 'uses' => 'StatisticsController@penalties'
]);
Route::get('statistics/team_players/{id}', [
    'as' => 'team_players', 'uses' => 'StatisticsController@team_players'
]);
Route::get('statistics/top5', [
    'as' => 'top5', 'uses' => 'StatisticsController@top5_goalies'
]);
Route::get('statistics/golies', [
	'as' => 'golies', 'uses' => 'StatisticsController@golies_stats'
]);
Route::get('statistics/referees', [
	'as' => 'referees', 'uses' => 'StatisticsController@referee_top'
]);
Route::get('statistics/longest-games', [
	'as' => 'longest-games', 'uses' => 'StatisticsController@longest_games'
]);
Route::get('statistics/fastest-goals', [
	'as' => 'fastest-games', 'uses' => 'StatisticsController@fastest_goals'
]);
Route::group(['middleware' => ['web']], function () {
    // your routes here
	Route::get('data/upload', [
		'as' => 'upload', 'uses' => 'UploadController@upload'
	]);

	Route::post('data/upload', [
		'as' => 'store', 'uses' => 'UploadController@store'
	]);

	Route::get('data/list', [
		'as' => 'list', 'uses' => 'UploadController@import'
	]);

	Route::get('import/{file}', [
    	'as' => 'import', 'uses' => 'Import@json'
	]);
});
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});
