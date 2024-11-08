<?php
    include 'backend/logic.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/styles.css">
    <title>Tic-Tac-Toe</title>
</head>

<body>
    <h1>Tic-Tac-Toe by IAN PURIFICACION</h1>

    <form method="post">
        <div class="board">
            <?php for ($i = 0; $i < 9; $i++): ?>
            <button type="submit" name="cell" value="<?php echo $i; ?>"
                class="cell <?php echo $_SESSION['board'][$i] ? 'filled' : ''; ?>"
                <?php echo $_SESSION['board'][$i] || $_SESSION['game_over'] ? 'disabled' : ''; ?>>
                <?php echo $_SESSION['board'][$i]; ?>
            </button>
            <?php endfor; ?>
        </div>
    </form>

    <div class="message">
        <?php
    if ($_SESSION['game_over']) {
        if ($_SESSION['winner'] === 'Draw') {
            echo "It's a draw!";
        } else {
            echo "Player {$_SESSION['winner']} wins!";
        }
    } else {
        echo "Current Player: " . $_SESSION['current_player'];
    }
    ?>
    </div>

    <form method="post">
        <button type="submit" id="btn" name="reset">Restart Game</button>
    </form>


</body>

</html>