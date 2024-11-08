<?php
    // Load the game board
    session_start();
    if (!isset($_SESSION['board'])) {
        $_SESSION['board'] = array_fill(0, 9, ''); // 3x3 grid flattened to 1D array
        $_SESSION['current_player'] = 'X';
        $_SESSION['game_over'] = false;
        $_SESSION['winner'] = null;
    }

    function checkWinner($board) {
        // Define all possible wins
        $win_combinations = [
            [0, 1, 2], [3, 4, 5], [6, 7, 8], // Rows
            [0, 3, 6], [1, 4, 7], [2, 5, 8], // Columns
            [0, 4, 8], [2, 4, 6]             // Diagonals
        ];

        foreach ($win_combinations as $combination) {
            if ($board[$combination[0]] !== '' &&
                $board[$combination[0]] === $board[$combination[1]] &&
                $board[$combination[1]] === $board[$combination[2]]) {
                return $board[$combination[0]]; // Return the winner
            }
        }

        return null; // No winner yet
    }

    function checkDraw($board) {
        return !in_array('', $board); // Draw if no empty spaces
    }

    // Handle move if the game isn't over
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cell']) && !$_SESSION['game_over']) {
        $cell = (int)$_POST['cell'];
        
        // Check if the cell is empty before making a move
        if ($_SESSION['board'][$cell] === '') {
            $_SESSION['board'][$cell] = $_SESSION['current_player'];

            // Check for a winner
            $_SESSION['winner'] = checkWinner($_SESSION['board']);
            if ($_SESSION['winner']) {
                $_SESSION['game_over'] = true;
            } elseif (checkDraw($_SESSION['board'])) {
                $_SESSION['game_over'] = true; // End game if it's a draw
                $_SESSION['winner'] = 'Draw';
            } else {
                // Switch player
                $_SESSION['current_player'] = $_SESSION['current_player'] === 'X' ? 'O' : 'X';
            }
        }
    }

    // Reset game
    if (isset($_POST['reset'])) {
        $_SESSION['board'] = array_fill(0, 9, '');
        $_SESSION['current_player'] = 'X';
        $_SESSION['game_over'] = false;
        $_SESSION['winner'] = null;
    }
?>