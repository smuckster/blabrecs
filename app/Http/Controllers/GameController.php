<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Tile;
use App\Models\Player;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function play(Request $request)
    {
        $request->validate([
            'game' => 'required',
            'player' => 'required'
        ]);

        $game = Game::where('code', $request->game)->first();
        $player = Player::where('code', $request->player)->first();
        $currentTurnCode = Player::find($game->current_turn)->code;

        return view('game')->with(['game' => $game,
                                   'player' => $player,
                                   'current_turn' => $currentTurnCode]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('newgame');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function new(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'playercount' => 'required'
        ]);

        $game = Game::make();

        $game->name = $request->name;
        $game->player_count = (integer)$request->playercount;
        $game->code = $this->generateGameCode();
        $game->remaining_tiles = json_encode($this->generateNewTileset());
        $game->current_board = json_encode($this->generateNewBoard());

        $game->save();

        $order = 1;
        foreach($request->playernames as $player) {
            if(!is_null($player)) {
                $player = Player::create(['name' => $player,
                                'game_id' => $game->id,
                                'code' => $this->generatePlayerCode(),
                                'order' => $order,
                                'rack' => '[]',
                                'score' => 0]);

                if($order == 1) {
                    $player->admin = 1;
                    $player->save();
                }

                $order++;
            }
        }

        $game->current_turn = $game->players->first()->id;
        $game->save();

        foreach($game->players as $player) {
            // Draw player tiles
            $player->rack = $this->drawTiles($game, 7);
            $player->save();
        }

        return redirect()->route('lobby', ['gamecode' => $game->code,
                              'playercode' => $game->players->first()->code,
                              'gameready' => true]);

        //$newRequest = new Request();
        //$newRequest->setMethod('POST');
        //$newRequest->request->add(['game' => $game->code, 
                                   //'player' => $game->players->first()->code,
                                   //'gamecode' => $game]);

        //return $this->play($newRequest);
    }

    private function generateGameCode() {
        $code = strtoupper(Str::random(4));
        if(sizeof(Game::where('code', $code)->get()) > 0) {
            $this->generateGameCode();
        }
        return $code;
    }

    private function generatePlayerCode() {
        $code = strtoupper(Str::random(4));
        if(sizeof(Player::where('code', $code)->get()) > 0) {
            $this->generatePlayerCode();
        }
        return $code;
    }

    private function generateNewTileset() {
        $tileDistribution = ['A' => ['count' => 9, 'value' => 1],
                            'B' => ['count' => 2, 'value' => 3],
                            'C' => ['count' => 2, 'value' => 3],
                            'D' => ['count' => 4, 'value' => 2],
                            'E' => ['count' => 12,'value' => 1],
                            'F' => ['count' => 2, 'value' => 4],
                            'G' => ['count' => 3, 'value' => 2],
                            'H' => ['count' => 2, 'value' => 4],
                            'I' => ['count' => 9, 'value' => 1],
                            'J' => ['count' => 1, 'value' => 8],
                            'K' => ['count' => 1, 'value' => 5],
                            'L' => ['count' => 4, 'value' => 1],
                            'M' => ['count' => 2, 'value' => 3],
                            'N' => ['count' => 6, 'value' => 1],
                            'O' => ['count' => 8, 'value' => 1],
                            'P' => ['count' => 2, 'value' => 3],
                            'Q' => ['count' => 1, 'value' => 10],
                            'R' => ['count' => 6, 'value' => 1],
                            'S' => ['count' => 4, 'value' => 1],
                            'T' => ['count' => 6, 'value' => 1],
                            'U' => ['count' => 4, 'value' => 1],
                            'V' => ['count' => 2, 'value' => 4],
                            'W' => ['count' => 2, 'value' => 4],
                            'X' => ['count' => 1, 'value' => 8],
                            'Y' => ['count' => 2, 'value' => 4],
                            'Z' => ['count' => 1, 'value' => 10],
                            '~' => ['count' => 2, 'value' => 0]];

        $tileset = array();
        foreach($tileDistribution as $letter => $attributes) {
            for($i = 1; $i <= $attributes['count']; $i++) {
                //$tileset[] = ['letter' => $letter,
                              //'value' => $attributes['value']];
                $tileset[] = $letter;
            }
        }

        return $tileset;
    }

    private function generateNewBoard() {
        $board = array();
        for($row = 0; $row <= 14; $row++) {
            for($col = 0; $col <= 14; $col++) {
                $board[$row][$col] = 0;
            }
        }

        return $board;
    }

    public function getBoard(Game $game) {
        return response(['board' => $game->current_board], 200);
    }

    public function saveBoard(Request $request, Game $game) {
        $game->current_board = $request->board;
        $game->save();

        return response(['board' => json_encode($game->current_board)], 200);
    }

    public function getScores(Game $game) {
        $scores = array();
        foreach($game->players as $player) {
            $scores[$player->code] = $player->score;
        }

        $tileCount = sizeof(json_decode($game->remaining_tiles, true));

        return response(['scores' => $scores, 'tile_count' => $tileCount], 200);
    }

    public function updateScore(Request $request, Game $game) {
        $currentPlayer = Player::find($game->current_turn);
        (integer)$currentPlayer->score += (integer)$request->score;
        $currentPlayer->save();

        return response(200);
    }

    // Returns a 200 status if it is the current player's turn.
    // Otherwise returns a 201 status.
    public function myTurn(Request $request, Game $game) {
        return ($request->playercode == Player::find($game->current_turn)->code) ? response(null, 200) : response(null, 201);
    }

    // Returns the player code for the game's current player
    public function getCurrentPlayer(Game $game) {
        return response(['code' => Player::find($game->current_turn)->code], 200);
    }

    public function passTurn(Game $game) {
        $currentPlayer = Player::find($game->current_turn);
        $this->resetRack($game, $currentPlayer);
    }

    // Changes the $game->current_player variable to the next player
    public function endTurn(Game $game) {
        $numberOfPlayers = $game->player_count;
        $currentPlayer = Player::find($game->current_turn);

        $this->refillRack($game, $currentPlayer);

        if($currentPlayer->order == $numberOfPlayers) {
            $nextPlayer = $game->players->where('order', 1)->first();
        } else {
            $nextPlayer = $game->players->where('order', ((integer)$currentPlayer->order + 1))->first();
        }

        $game->current_turn = $nextPlayer->id;
        $game->save();

        return response(['rack' => $currentPlayer->rack], 200);
    }

    // Returns a JSON-encoded array
    public function getRack(Request $request) {
        $player = Player::where('code', $request->code)->first();
        return response(['rack' => $player->rack], 200);
    }

    private function resetRack(Game $game, Player $player) {
        $rack = json_decode($player->rack, true);
        $bag = json_decode($game->remaining_tiles, true);

        // Return the rack tiles to the bag
        $bag = array_merge($rack, $bag);

        // Empty the rack and redraw 7 tiles
        $rack = array();
        $rack = $this->drawTiles($game, 7);

        $player->rack = json_encode($rack);

        $player->save();
    }

    public function saveRack(Request $request) {
        $player = Player::where('code', $request->code)->first();
        $player->rack = $request->rack;
        $player->save();

        return response(200);
    }

    // Returns an array of one or more tiles after removing them
    // from the array of remaining tiles in the game.
    private function drawTiles($game, $number = 1) {
        $bag = (array)json_decode($game->remaining_tiles, true);
        $tiles = array();
        //Log::info('Type of $bag variable: ' . gettype($bag));
        if($number > 1 && sizeof($bag) >= $number) {
            $tile_keys = array_rand($bag, $number);
            foreach($tile_keys as $key) {
                $tiles[] = $bag[$key];
                unset($bag[$key]);
            }
        } elseif($number == 1 && sizeof($bag) >= 1) {
            $tile_key = array_rand($bag, $number);
            $tiles[] = $bag[$tile_key];
            unset($bag[$tile_key]);
        } elseif($number != 0) {
            foreach($bag as $bagtile) {
                $tiles[] = $bagtile;
            }
            $bag = array();
        }

        $game->remaining_tiles = json_encode(array_values($bag));
        $game->save();

        return $tiles;
    }

    private function refillRack($game, $player) {
        $rackTiles = json_decode($player->rack, true);
        //Log::info('Rack Tiles variable: ');
        //Log::info(print_r($rackTiles));
        $tilesToDraw = 7 - sizeof($rackTiles);
        $drawnTiles = $this->drawTiles($game, $tilesToDraw);
        $allTiles = array_merge($rackTiles, $drawnTiles);
        $player->rack = json_encode($allTiles);
        $player->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function show(Game $game)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function edit(Game $game)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Game $game)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function destroy(Game $game)
    {
        //
    }
}
