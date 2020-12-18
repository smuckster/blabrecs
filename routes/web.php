<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'LobbyController@index');
Route::get('/game/new', 'GameController@create');
Route::post('/game/new', 'GameController@new');
Route::get('/signin', 'GameController@signin');
Route::any('/game', 'GameController@play');
Route::get('/game/{game}/board', 'GameController@getBoard');
Route::post('/game/{game}/board', 'GameController@saveBoard');
Route::get('/game/{game}/scores', 'GameController@getScores');
Route::post('/game/{game}/score', 'GameController@updateScore');
Route::post('/game/{game}/myturn', 'GameController@myTurn');
Route::post('/game/{game}/endturn', 'GameController@endTurn');
Route::post('/game/{game}/passturn', 'GameController@passTurn');
Route::get('/game/{game}/currentplayer', 'GameController@getCurrentPlayer');
Route::post('/player/rack', 'GameController@getRack');
Route::post('/player/rack/save', 'GameController@saveRack');
