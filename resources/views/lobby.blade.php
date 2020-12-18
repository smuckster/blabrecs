@extends('layout')

@section('content')

<div class="lobby-container">

    <h1>Blabrecs</h1>

    <p class="instructions">Start a new game and invite other players to join<br> or join a game that's already in progress.</p>

    <a href="/game/new"><button class="btn new-game">Start a game</button></a>

    <p class="instructions">To join a game, enter the game code and your player code,<br> then click the "Join a game" button.</p>

    <form class="join-game-form" method="post" action="/game">
        @csrf

        <label for="game-code">Game Code:</label>
        <input type="text" class="join-input" id="game-code" name="game" required>

        <br>

        <label for="player-code">Player Code:</label>
        <input type="text" class="join-input" id="player-code" name="player" required>

        <input type="submit" class="btn join-game" value="Join a game">
    </form>


</div>

@endsection
