@extends('layout')

@section('content')

<div class="lobby-container">

    <h1>Create a new game</h1>

    <form action="/game/new" method="post" class="newgame-form">
        @csrf

        <label for="name">Game name:</label>
        <input type="text" id="name" name="name" class="new-input" required>

        <br>

        <label for="playercount">How many players?</label>
        <select id="playercount" name="playercount" class="new-select">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4" selected>4</option>
        </select>

        <p class="instructions">You will automatically join the game as the first player, so enter your own name first.</p>

        <div class="player-container">
        <label for="player1name">First player's name:</label>
        <input type="text" id="player1name" name="playernames[]" class="new-input player-name">
        </div>

        <div class="player-container">
        <label for="player2name">Second player's name:</label>
        <input type="text" id="player2name" name="playernames[]" class="new-input player-name">
        </div>

        <div class="player-container">
        <label for="player3name">Third player's name:</label>
        <input type="text" id="player3name" name="playernames[]" class="new-input player-name">
        </div>

        <div class="player-container">
        <label for="player4name">Fourth player's name:</label>
        <input type="text" id="player4name" name="playernames[]" class="new-input player-name">
        </div>

        <input type="submit" value="Create" class="btn new-game">
    </form>

</div>

@endsection
