<?php
session_start();

// Initialize game state if not already set
if (!isset($_SESSION['game'])) {
    initializeGame();
}

// Process form requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['move'])) {
        handleMove((int)$_POST['move']);
    } elseif (isset($_POST['restart'])) {
        initializeGame();
    }
}

// Reset the game state
function initializeGame()
{
    $_SESSION['game'] = [
        'board' => array_fill(0, 9, null),
        'current_player' => 'X',
        'game_history' => $_SESSION['game']['game_history'] ?? [],
        'result' => null,
    ];
}

// Handle a player move
function handleMove($index)
{
    $game = &$_SESSION['game'];
    if ($game['board'][$index] === null && $game['result'] === null) {
        $game['board'][$index] = $game['current_player'];
        $game['result'] = checkResult($game['board']);
        if ($game['result']) {
            $game['game_history'][] = "Game " . (count($game['game_history']) + 1) . ": " . $game['result'];
        } else {
            $game['current_player'] = $game['current_player'] === 'X' ? 'O' : 'X';
        }
    }
}

// Check for result
function checkResult($board)
{
    $winningCombinations = [
        [0, 1, 2], [3, 4, 5], [6, 7, 8], // Rows
        [0, 3, 6], [1, 4, 7], [2, 5, 8], // Cols
        [0, 4, 8], [2, 4, 6],           // Diags
    ];

    foreach ($winningCombinations as [$a, $b, $c]) {
        if ($board[$a] && $board[$a] === $board[$b] && $board[$a] === $board[$c]) {
            return "{$board[$a]} won!";
        }
    }

    return in_array(null, $board, true) ? null : "Draw!";
}

// Extract game variables for use in the view
$game = $_SESSION['game'];
$board = $game['board'];
$currentPlayer = $game['current_player'];
$result = $game['result'];
$gameHistory = $game['game_history'];
?>