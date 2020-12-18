@extends('layout')

@section('content')

<div class="game-container" data-id="{{ $game->id }}" data-turn="{{ $current_turn }}">

    <div class="game-header">
        <div class="game-name">{{ $game->name }} ({{ $game->code }})<span class="updating"><img src="loading.svg">Updating board...</span></div>
        <div class="playing-as">Playing as <b>{{ $player->name }} (<span id="current-player-code">{{ $player->code }}</span>)</b></div>
    </div>

    <div class="board">
        @for ($row = 0; $row <= 14; $row++)
        <div class="row">
            @for ($col = 0; $col <= 14; $col++)
            <div class="square {{ $game->board[$row][$col] }}"
                data-x="{{ $col }}"
                data-y="{{ $row }}"
                data-letter="0">
            @switch ($game->board[$row][$col])
                @case('tw')
                    <span>TRIPLE WORD SCORE</span>
                    @break
                @case('dl')
                    <span>DOUBLE LETTER SCORE</span>
                    @break
                @case('dw')
                    <span>DOUBLE WORD SCORE</span>
                    @break
                @case('tl')
                    <span>TRIPLE LETTER SCORE</span>
                    @break
                @case('mi')
                    <img src="/star.svg">
                    @break
            @endswitch
            </div>
            @endfor
        </div>
        @endfor
    </div>

    <div class="side-column">

    <div class="board-tiles"></div>

    <div class="your-turn">It's your turn!<span class="saving-move"><img src="loading.svg">Saving move...</span></div>

    <div class="rack">
        <!--<div class="rack-label">Your letters</div>
        <div class="tile 45 moveable" data-x="" data-y=""><div class="letter">M</div><div class="points">3</div></div>
        <div class="tile 46 moveable" data-x="" data-y=""><div class="letter">S</div><div class="points">1</div></div> -->
    </div>

    <br>
    <button class="btn-sm new-game save-move">Save move</button>
    <button class="btn-sm return-tiles">Return tiles</button>
    <!--<button class="btn-sm join-game pass-turn">Pass and draw new tiles</button>-->

    <br>
    <div class="score-input-container">
        <label for="score-input">Score for this move:</label>
        <input type="number" id="score-input" min="0" max="500" value="0">
        <button class="btn-sm new-game submit-score">Submit score</button>
    </div>

    <div class="admin-panel">
        <div class="admin-heading">Players</div>
        @foreach($game->players as $listplayer)
            <div class="admin-player @if($listplayer->id == $game->current_turn) current-turn @endif" data-code="{{ $listplayer->code }}">
                <div>{{ $listplayer->name }}
                @if($player->admin == 1)
                    ({{ $listplayer->code }})
                @endif
                </div>
                <div class="score">{{ $listplayer->score }}</div>
            </div>
        @endforeach
        <div class="admin-heading" style="margin-top: 16px;"><span class="tile-count">{{ sizeof(json_decode($game->remaining_tiles, true)) }}</span> remaining tiles</div>
    </div>

    <div class="blabrecs-container">
        <div style="margin-left:4px;">Check your word against the official Blabrecs AI.</div>
        <div class="blabrecs-frame">
            <iframe src="https://mkremins.github.io/blabrecs/" scrolling="no"></iframe>
        </div>
    </div>

    <div><b>Instructions</b></div>
    <ul class="instructions-left">
        <li>To move a tile to the board, click on the tile to select it, then click on the square on the board you'd like to place it on.</li>
        <li>When you have placed all of the tiles for your word, click the "Save move" button, then enter your score for the turn and click "Submit score". Avoid the temptation to fudge the numbers in your favor, else your honor shall surely be besmirched.</li>
        <li>You may return all of the tiles you placed this turn to your rack by clicking the "Return tiles" button.</li>
        <!--<li>You may discard your entire rack and draw a new set of tiles by clicking the "Pass and draw new tiles" button. Note that this will forfeit your turn!</li>-->
    </ul>

</div>

</div>

@endsection
