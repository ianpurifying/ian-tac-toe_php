<?php 
    include "logic.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpeg" href="styles/images/chillguy.png">
    <link rel="stylesheet" href="styles/styles.css">
    <title>IAN-CHILL-TOE</title>
</head>

<body>
    <div class="header">
        <img src="styles/images/chillguy.png" alt="Game Logo" class="logo">
        <div class="title">IAN-CHILL-TOE</div>
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
            <div class="footer-logo">
                <a href="https://cdscdb.edu.ph/" target="_blank"><img src="styles/images/cdscdb_logo.jpg" alt="Logo" class="logo"></a> 
            </div>
            
            <div class="footer-links">
                <p>Made with 👺 by <a href="https://facebook.com/ianpurifying" target="_blank">IAN PURIFICACION</a></p>
                <p>&copy; <?= date("Y") ?>. All rights reserved.</p>
                <div class="logo-links">
                    <a href="https://facebook.com/ianpurifying" target="_blank"><img src="styles/images/fb_logo.png" alt="Facebook" class="social-logo"></a>
                    <a href="https://twitter.com/elonmusk" target="_blank"><img src="styles/images/x_logo.png" alt="Twitter" class="social-logo"></a>
                    <a href="https://instagram.com/zuck" target="_blank"><img src="styles/images/ig_logo.png" alt="Instagram" class="social-logo"></a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>