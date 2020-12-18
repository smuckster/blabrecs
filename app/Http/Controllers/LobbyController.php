<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LobbyController extends Controller
{
    public function index(Request $request) {
        $gamecode = $request->has('gamecode') ? $request->gamecode : '';
        $playercode = $request->has('playercode') ? $request->playercode : '';
        $gameready = $request->has('gameready') ? $request->gameready : false;

        return view('lobby')->with(['gamecode' => $gamecode,
                                    'playercode' => $playercode,
                                    'gameready' => $gameready]);
    }
}
