let board;
let rack;
let currentPlayerCode;
let letterValues = {'A': 1,
                    'B': 3,
                    'C': 3,
                    'D': 2,
                    'E': 1,
                    'F': 4,
                    'G': 2,
                    'H': 4,
                    'I': 1,
                    'J': 8,
                    'K': 5,
                    'L': 1,
                    'M': 3,
                    'N': 1,
                    'O': 1,
                    'P': 3,
                    'Q': 10,
                    'R': 1,
                    'S': 1,
                    'T': 1,
                    'U': 1,
                    'V': 4,
                    'W': 4,
                    'X': 8,
                    'Y': 4,
                    'Z': 10,
                    ' ': 0};

let interval;

$(document).ready(function() {

    // Call each of these once when the page loads
    //addTileEvents();
    getBoardState();
    renderBoard();
    renderRack();
    isItMyTurn();

    // From here on out we'll be getting page updates from
    // the interval function.

    // Show/hide player name inputs on new game form
    $('#playercount').change(function() {
        playerCount = Number($(this).val());
        iter = 1;

        $('.player-container').hide();
        $('.player-container input').prop('required', false);

        $('.player-container').each(function() {
            if(iter <= playerCount) {
                $(this).show();
                $(this).children('input').prop('required', true);
            }
            iter += 1;
        });
    });

    // Save move button
    $('.save-move').click(function() {
        pauseUpdates();
        updateBoardStateVariable();
        saveBoardState();
        $('.score-input-container').slideDown();
    });

    // Save score button
    $('.submit-score').click(function() {
        $('.saving-move').css('visibility', 'visible');
        $('.score-input-container').slideUp();
        updatePlayerScore($('#score-input').val());
        endPlayerTurn();
    });

    // Return tiles button
    $('.return-tiles').click(function() {
        returnTilesToRack();
        addTileEvents();
    });

    // Pass turn button
    $('.pass-turn').click(function() {
        gameId = $('.game-container').data('id');
        $('.saving-move').css('visibility', 'visible');
        disableButtons();

        axios.post('/game/' + gameId + '/passturn')
            .then(response => {
                renderRack();
                endPlayerTurn();
            });
    });

});

function pauseUpdates() {
    clearInterval(interval);
}

function restartUpdates() {
    interval = setInterval(checkForUpdates, 1500);
}

function checkForUpdates() {
    let gameIdNum = $('.game-container').data('id');
    axios.get('/game/' + gameIdNum + '/currentplayer')
        .then(response => {
            if(response.data.code != currentPlayerCode) {
                pauseUpdates();
                updateCurrentPlayer(response.data.code);
                isItMyTurn();
                getBoardState();
                renderBoard();
                renderRack();
            }
        });
}

function updateCurrentPlayer(code) {
    currentPlayerCode = code;
    $('.game-container').attr('data-turn', code);
    $('.admin-player').removeClass('current-turn');
    $(`.admin-player[data-code='${code}']`).addClass('current-turn');
}

function addTileEvents() {
    // Toggle tile selection
    $('.rack .tile').click(function() {
        // Only make tiles selectable if they came from the player's rack
        if(!$(this).hasClass('placed')) {
            if($(this).hasClass('selected')) {
                // Unselect a tile
                $(this).removeClass('selected');

                // Stop dragging the tile around the board
                $('.square').unbind('mouseenter mouseleave');

                // Update the board space's data to reflect
                // the tile being positioned on it.
                let tile = $(this);
                let setx = $(this).data('x');
                let sety = $(this).data('y');
                let square = $('.board .row:nth-child(' + (sety + 1) + ') .square:nth-child(' + (setx + 1) + ')');
                square.attr('data-letter', tile.children('.letter').text());
                square.attr('data-score', tile.children('.points').text());
            } else {
                $('.tile').removeClass('selected');
                $(this).addClass('selected');
                let tile = $(this);
                let oldSquare = $('.board .row:nth-child(' + (Number(tile.attr('data-y')) + 1) + ') .square:nth-child(' + (Number(tile.attr('data-x')) + 1) + ')');
                oldSquare.attr('data-letter', '0');
                oldSquare.attr('data-score', '0');

                // Add hover effect to empty board spaces
                $('.square').hover(function() {
                    square = $(this);
                    y = $(this).position().left;
                    x = $(this).position().top;
                    tile.offset({top: x, left: y+1});
                    tile.attr('data-x', square.data('x'));
                    tile.attr('data-y', square.data('y'));
                }, function() {});
            }
        }
    });
}

function removeTileEvents() {
    $('.rack .tile').off();
}

function disableButtons() {
    $('button').prop('disabled', true);
}

function enableButtons() {
    $('button').prop('disabled', false);
}

function getBoardState() {
    const gameId = $('.game-container').data('id');
    axios.get('/game/' + gameId + '/board')
        .then(response => board = JSON.parse(response.data.board));
}

function updateBoardStateVariable() {
    for(let row = 0; row <= 14; row++) {
        for(let col = 0; col <= 14; col++) {
            board[row][col] = $('.board .row:nth-child(' + (Number(row) + 1) + ') .square:nth-child(' + (Number(col) + 1) + ')').attr('data-letter');
        }
    }
}

function saveBoardState() {
    // Add the "placed" class to any tiles the player placed on the board
    $('.rack .tile').each(function() {
        if($(this).attr('data-x') != '') {
            $(this).addClass('placed');
        }
    });

    const gameId = $('.game-container').data('id');
    axios.post('/game/' + gameId + '/board', {board: JSON.stringify(board)});
}

function getScores() {
    const gameId = $('.game-container').data('id');
    axios.get('/game/' + gameId + '/scores')
        .then(response => {
            let scores = response.data.scores;
            $('.admin-player').each(function() {
                $(this).children('.score').text(scores[$(this).data('code')]);
            });
            $('.tile-count').text(response.data.tile_count);
        });
}

function updatePlayerScore(score) {
   const gameId = $('.game-container').data('id');
   axios.post('/game/' + gameId + '/score', {score: Number(score)})
    .then(response => {
        if(response.status == 200) {
            $('.score-input-container').slideUp();
        }
    });
}

function endPlayerTurn() {
    // Remove tiles that were just played from player's rack.
    // Save the new rack.
    saveRack(function() {
        // Once the updated rack has been fully saved,
        // submit request to end turn
        const gameId = $('.game-container').data('id');
        axios.post('/game/' + gameId + '/endturn')
            .then(() => {
                isItMyTurn();
                getBoardState();
                renderBoard();
                renderRack();
                $('.saving-move').css('visibility', 'hidden');
            });
    });
}

function isItMyTurn() {
    const gameId = $('.game-container').data('id');
    let playerCode = $('#current-player-code').text();
    axios.post('/game/' + gameId + '/myturn', {playercode: playerCode})
        .then(response => {
            if(response.status == 200) {
                // It is your turn, no need to do anything
                $('.your-turn').css('color', 'green');
                removeTileEvents();
                addTileEvents();
                enableButtons();

                // Stop listening for updates
                pauseUpdates();
            } else {
                // It is someone else's turn, disable buttons
                // and stop tile dragging events.
                removeTileEvents();
                disableButtons();
                $('.your-turn').css('color', 'white');

                // Start listening for updates
                pauseUpdates();
                restartUpdates();
            }
        })
}

function renderBoard() {
    const gameId = $('.game-container').data('id');

    $('.updating').css('visibility', 'visible');

    axios.get('/game/' + gameId + '/board')
        .then(response => {
            returnTilesToRack();

            clearBoardTiles();

            for(let row = 0; row <= 14; row++) {
                for(let col = 0; col <= 14; col++) {
                    // If this space isn't empty, put a tile on it
                    if(board[row][col] != 0) {
                        if(board[row][col] == '!') { board[row][col] = ' '; }
                        tile = `<div class="tile placed" data-x="" data-y=""><div class="letter">${board[row][col]}</div><div class="points">${getLetterValue(board[row][col])}</div></div>`;

                        square = $('.board .row:nth-child(' + (Number(row) + 1) + ') .square:nth-child(' + (Number(col) + 1) + ')');
                        y = square.position().left;
                        x = square.position().top;
                        square.attr('data-letter', board[row][col]);

                        // Create the new tile and place it on the board appropriately
                        let newTile = $(tile);
                        newTile.appendTo('.board-tiles');
                        newTile.offset({top: x, left: y+1});
                        newTile.attr('data-x', square.data('x'));
                        newTile.attr('data-y', square.data('y'));
                    }
                }
            }

            getScores();

            $('.updating').css('visibility', 'hidden');
        });
}

function clearBoardTiles() {
    $('.tile.placed').remove();
}

function renderRack() {
    const playerCode = $('#current-player-code').text();

    axios.post('/player/rack', {code: playerCode})
        .then(response => {
            let rack = JSON.parse(response.data.rack);

            // Remove tiles currently in rack
            $('.rack .tile').remove();

            // Create new elements for current rack tiles
            rack.forEach(tile => {
                if(tile == '~') { tile = ' '; }
                let tileHTML = `<div class="tile moveable" data-x="" data-y=""><div class="letter">${tile}</div><div class="points">${getLetterValue(tile)}</div></div>`;

                let newTile = $(tileHTML);
                newTile.appendTo('.rack');
            });

            removeTileEvents();
            isItMyTurn();
        });
}

function saveRack(callback) {
    const playerCode = $('#current-player-code').text();
    let newRack = [];
    $('.rack .tile').not('.placed').each(function() {
        newRack.push($(this).children('.letter').text());
    });
    axios.post('/player/rack/save', {code: playerCode, rack: JSON.stringify(newRack)})
        .then(response => {
            if(response.status == 200) {
                callback();
            }
        });
}

function returnTilesToRack() {
    $('.rack .tile').each(function() {
        if($(this).attr('data-x') != '') {
            $(this).clone().appendTo('.rack').css({'top': 'initial', 'left': 'initial'});
            $(this).remove();

            removeTileEvents();
        }
    });
}

function getLetterValue(letter) {
    return letterValues[letter];
}
