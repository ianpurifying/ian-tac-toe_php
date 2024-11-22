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

// Initialize or reset the game state
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

// Check for win, draw, or ongoing game
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpeg" href="images/cat.jpg">
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
            background: linear-gradient(135deg, #ffcb77, #f7a5a1);
            color: #f53c2b;
            overflow: hidden;
        }

        /* Header Styles */
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px 0;
        }

        .logo {
            width: 100px;
            height: 85px;
            border-radius: 30px;
            margin-right: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            object-fit: cover;
        }

        .title {
            font-size: 4rem;
            font-weight: bold;
            color: #f53c2b;
            text-align: center;
            position: relative;
            display: inline-block;
            padding: 10px 20px;
            border-radius: 10px;
            background: linear-gradient(45deg, #ff7f50, #ffcc00);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .title::before,
        .title::after {
            content: '';
            position: absolute;
            background-color: #ffffff;
            border-radius: 50%;
            animation: cloudAnimation 10s infinite ease-in-out;
        }

        .title::before {
            top: 50%;
            left: 50%;
            width: 200px;
            height: 80px;
            transform: translate(-50%, -50%);
        }

        .title::after {
            top: 20%;
            left: 70%;
            width: 250px;
            height: 100px;
            transform: translate(-50%, -50%);
        }

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

        /* Container Styles */
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
            border-right: 2px solid #ff9f68;
            background: linear-gradient(135deg, #ff9f68, #ffcb77);
            display: flex;
            flex-direction: column;
        }

        .game-history h3 {
            font-size: 2rem;
            text-align: center;
            color: #f53c2b;
            margin-bottom: 15px;
        }

        .game-history .history-table {
            max-height: 400px;
            overflow-y: auto;
        }

        .game-history table {
            width: 100%;
            border-collapse: collapse;
        }

        .game-history td {
            padding: 10px;
            font-size: 1.2rem;
            text-align: left;
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
            transition: background-color 0.3s ease-in-out;
        }

        .modal-content button:hover {
            background-color: #d0391f;
        }

        .modal.show {
            display: flex;
        }

        /* Footer Styles */
        footer {
            width: 100%;
            height: 100%;
            background-color: #333;
            color: #fff;
            padding: 20px 0;
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }

        /* Footer container for layout */
        .footer-container {
            width: 90%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
        }

        /* Left side logo */
        .footer-logo .logo {
            width: 50px;
            height: auto;
            border-radius: 50%;
            transition: transform 0.3s ease-in-out;
        }

        .footer-logo .logo:hover {
            transform: scale(1.1);
        }

        /* Footer Links */
        .footer-links {
            text-align: center;
        }

        /* Social media logo links */
        .logo-links {
            margin-top: 10px;
        }

        .logo-links a {
            margin: 0 10px;
            display: inline-block;
        }

        .social-logo {
            width: 30px;
            height: 30px;
            transition: transform 0.3s ease-in-out;
        }

        .social-logo:hover {
            transform: scale(1.2);
        }

        footer a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }

        footer .copyright {
            font-size: 1rem;
            margin-top: 10px;
            font-style: italic;
        }

    </style>

</head>
<body>
    <div class="header">
        <img src="images/cat.jpg" alt="Game Logo" class="logo">
        <div class="title">IAN-TAC-TOE</div>
    </div>

    <div class="container">
        <!-- Game History -->
        <div class="game-history">
            <h3>Game History</h3>
            <div class="history-table">
                <table>
                    <tbody>
                        <?php foreach ($gameHistory as $history): ?>
                            <tr><td><?= htmlspecialchars($history) ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Game Board -->
        <div class="game-board">
            <form method="POST">
                <div id="board">
                    <?php foreach ($board as $i => $cell): ?>
                        <button class="cell <?= $cell ? 'taken' : '' ?>" 
                                name="move" 
                                value="<?= $i ?>" 
                                <?= $cell ? 'disabled' : '' ?>>
                            <?= htmlspecialchars($cell ?? '') ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                <p id="player-turn">Current Turn: <?= htmlspecialchars($currentPlayer) ?></p>
            </form>
        </div>
    </div>

    <!-- Result Modal -->
    <div id="result-modal" class="modal <?= $result ? 'show' : '' ?>">
        <div class="modal-content">
            <p id="result-message"><?= htmlspecialchars($result) ?></p>
            <form method="POST">
                <button name="restart">Restart Game</button>
            </form>
        </div>
    </div>

    <!-- Footer Section -->
    <footer>
        <div class="footer-container">
            <!-- Left side: Logo Image -->
            <div class="footer-logo">
                <img src="images/dogs.png" alt="Logo" class="logo">
            </div>
            
            <!-- Right side: Logo Links -->
            <div class="footer-links">
                <p>Made with 👺 by <a href="https://facebook.com/ianpurifying" target="_blank">IAN PURIFICACION</a></p>
                <p>&copy; <?= date("Y") ?>. All rights reserved.</p>
                <div class="logo-links">
                    <a href="https://facebook.com/ianpurifying" target="_blank"><img src="images/fb_logo.png" alt="Facebook" class="social-logo"></a>
                    <a href="https://twitter.com/elonmusk" target="_blank"><img src="images/x_logo.png" alt="Twitter" class="social-logo"></a>
                    <a href="https://instagram.com/zuck" target="_blank"><img src="images/ig_logo.png" alt="Instagram" class="social-logo"></a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>