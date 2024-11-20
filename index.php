<?php
session_start();

// Initialize game history if not already set
if (!isset($_SESSION['game_history'])) {
    $_SESSION['game_history'] = [];
}

// Initialize game state if not already set
if (!isset($_SESSION['board'])) {
    $_SESSION['board'] = array_fill(0, 9, null);
    $_SESSION['current_player'] = 'X';
}

// Handle form submissions for restarting the game or saving results
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['move'])) {
        // Update the game board with the selected move
        $index = (int)$_POST['move'];
        if (!$_SESSION['board'][$index]) {
            $_SESSION['board'][$index] = $_SESSION['current_player'];
            $_SESSION['current_player'] = $_SESSION['current_player'] === 'X' ? 'O' : 'X';
        }

        // Check for game result (win or draw)
        $result = checkResult($_SESSION['board']);
        if ($result) {
            $_SESSION['game_history'][] = "Game " . (count($_SESSION['game_history']) + 1) . ": " . $result;
            $_SESSION['result'] = $result;
            $_SESSION['board'] = array_fill(0, 9, null); // Reset board
        }
    } elseif (isset($_POST['restart'])) {
        // Reset game board and turn
        $_SESSION['board'] = array_fill(0, 9, null);
        $_SESSION['current_player'] = 'X';
        unset($_SESSION['result']);
    }
}

// Function to check for win or draw
function checkResult($board) {
    $winningCombinations = [
        [0, 1, 2], [3, 4, 5], [6, 7, 8], // Rows
        [0, 3, 6], [1, 4, 7], [2, 5, 8], // Columns
        [0, 4, 8], [2, 4, 6],           // Diagonals
    ];

    foreach ($winningCombinations as $combination) {
        [$a, $b, $c] = $combination;
        if ($board[$a] && $board[$a] === $board[$b] && $board[$a] === $board[$c]) {
            return $board[$a] . " won!";
        }
    }

    if (!in_array(null, $board, true)) {
        return "Draw!";
    }

    return null; // Game is ongoing
}

$board = $_SESSION['board'];
$currentPlayer = $_SESSION['current_player'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IAN-TAC-TOE</title>
    <style>
        /* Base Styles */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #ffcb77; /* Warm, bold background color */
            color: #f53c2b; /* Bold text color */
            overflow: hidden;
            background: linear-gradient(135deg, #ffcb77, #f7a5a1); /* Warm gradient */
        }

        /* Container for Game History and Game Board */
        .container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            background-color: rgba(255, 255, 255, 0.85);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin-bottom: 40px;
        }

        /* Game History Styles */
        .game-history {
            width: 30%;
            padding: 20px;
            border-right: 2px solid #ff9f68; /* Bold border for separation */
            background: linear-gradient(135deg, #ff9f68, #ffcb77); /* Gradient background */
            display: flex;
            flex-direction: column;
        }

        .game-history h3 {
            font-size: 2rem;
            text-align: center;
            font-family: Arial, sans-serif;
            color: #f53c2b; /* Bold text color */
            margin-bottom: 15px;
        }

        .game-history .history-table {
            max-height: 400px;
            overflow-y: auto;
            margin-top: 10px;
        }

        .game-history table {
            width: 100%;
            border-collapse: collapse;
        }

        .game-history td {
            padding: 10px;
            font-size: 1.2rem;
            text-align: left;
            font-family: Arial, sans-serif;
            color: #333;
        }

        /* Game Board Styles */
        .game-board {
            flex: 1;
            padding: 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        #board {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-gap: 10px;
            margin: 20px auto;
            width: 300px;
            height: 300px;
        }

        .cell {
            display: flex;
            justify-content: center;
            align-items: center;
            border: 2px solid #f53c2b;
            background-color: #ffffffb3;
            font-size: 2rem;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-family: Arial, sans-serif;
            color: #f53c2b;
            border-radius: 15px;
            width: 90px;
            height: 90px;
        }

        .cell:hover {
            background-color: #ff9f68;
            transform: scale(1.05);
        }

        .cell.taken {
            pointer-events: none;
            background-color: #d3d3d3;
        }

        #player-turn {
            margin-top: 20px;
            font-size: 1.2rem;
            font-family: Arial, sans-serif;
            color: #f53c2b;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #ffffff;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .modal-content p {
            font-family: Arial, sans-serif;
            color: #f53c2b;
            font-size: 1.4rem;
        }

        .modal-content button {
            margin-top: 20px;
            padding: 12px 30px;
            background-color: #f53c2b;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-family: Arial, sans-serif;
            transition: background-color 0.3s ease-in-out;
        }

        .modal-content button:hover {
            background-color: #d0391f;
        }

        .modal.show {
            display: flex;
        }

        .title {
            font-size: 4rem; /* Bigger font size for prominence */
            font-weight: bold;
            font-family: Arial, sans-serif;
            color: #f53c2b; /* Bold color for the text */
            margin: 40px 0;
            text-align: center;
            position: relative;
            display: inline-block;
            padding: 10px 20px;
            border-radius: 10px;
            background: linear-gradient(45deg, #ff7f50, #ffcc00); /* Exaggerated gradient */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        /* Cloud-like animation behind the title */
        .title::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 200px;
            height: 80px;
            background-color: #ffffff;
            border-radius: 50%;
            animation: cloudAnimation 10s infinite ease-in-out;
            transform: translate(-50%, -50%);
        }

        /* Additional floating clouds */
        .title::after {
            content: '';
            position: absolute;
            top: 20%;
            left: 70%;
            width: 250px;
            height: 100px;
            background-color: #ffffff;
            border-radius: 50%;
            animation: cloudAnimation 12s infinite ease-in-out;
            transform: translate(-50%, -50%);
        }

        /* Cloud movement animation */
        @keyframes cloudAnimation {
            0% {
                transform: translateX(-100%) translateY(0);
            }
            50% {
                transform: translateX(100%) translateY(-20px);
            }
            100% {
                transform: translateX(-100%) translateY(0);
            }
        }

        /* Footer Section */
        footer {
            width: 100%;
            background-color: #333;
            color: #fff;
            padding: 20px 0;
            text-align: center;
            font-family: Arial, sans-serif;
            margin-top: 20px;
        }

        footer a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }

        footer .copyright {
            font-size: 1rem;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="title">IAN-TAC-TOE</div>

    <div class="container">
        <!-- Game History -->
        <div class="game-history">
            <h3>Game History</h3>
            <div class="history-table">
                <table>
                    <tbody>
                        <?php foreach ($_SESSION['game_history'] as $history): ?>
                            <tr>
                                <td><?= htmlspecialchars($history) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Game Board -->
        <div class="game-board">
            <form method="POST">
                <div id="board">
                    <?php for ($i = 0; $i < 9; $i++): ?>
                        <button class="cell <?= $board[$i] ? 'taken' : '' ?>" name="move" value="<?= $i ?>" <?= $board[$i] ? 'disabled' : '' ?>>
                            <?= htmlspecialchars($board[$i] ?? '') ?>
                        </button>
                    <?php endfor; ?>
                </div>
                <div id="player-turn">
                    <p>Current Turn: <span id="current-player"><?= htmlspecialchars($currentPlayer) ?></span></p>
                </div>
            </form>
        </div>
    </div>

    <!-- Result Modal -->
    <div id="result-modal" class="modal <?= isset($_SESSION['result']) ? 'show' : '' ?>">
        <div class="modal-content">
            <p id="result-message"><?= $_SESSION['result'] ?? '' ?></p>
            <form method="POST">
                <button name="restart">Restart Game</button>
            </form>
        </div>
    </div>

    <!-- Footer Section -->
    <footer>
        <p>Made with ❤️ by <a href="https://facebook.com/ianpurifying" target="_blank">IAN PURIFICACION</a></p>
        <p style="font-size: 0.8rem; font-style: italic;">Copyright <?= date("Y") ?>. All rights reserved.</p>
    </footer>
</body>
</html>