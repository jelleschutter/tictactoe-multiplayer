<?php

    include "config.php";
    include "session.php";

    $sql = "SELECT * FROM opponent WHERE user_id = '" . $_SESSION["user_id"] . "'";
    $gamesData = $db_connection->query($sql);

    $games = array();

    while ($game = $gamesData->fetch_assoc()) {

        $sql = "SELECT user.username FROM opponent INNER JOIN user ON opponent.user_id = user.user_id WHERE game_id = '" . $game["game_id"] . "' AND NOT opponent.user_id = " . $_SESSION["user_id"] . "";
        $opponentData = $db_connection->query($sql);
        $opponent = $opponentData->fetch_assoc();


        $sql = "SELECT COUNT(user_id) AS count FROM win WHERE game_id = '" . $game["game_id"] . "' AND user_id = " . $_SESSION["user_id"] . "";
        $winData = $db_connection->query($sql);
        $wins = $winData->fetch_assoc();

        $sql = "SELECT COUNT(user_id) AS count FROM win WHERE game_id = '" . $game["game_id"] . "' AND NOT user_id = " . $_SESSION["user_id"] . "";
        $lossesData = $db_connection->query($sql);
        $losses = $lossesData->fetch_assoc();

        array_push($games, array($game["game_id"], $opponent["username"], $wins["count"], $losses["count"], $status));
    }

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
    <link href="../Style.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<body>
    <header>
        <a href="NewGame">New Game</a>
        <a href="Logout">Logout</a>
    </header>
    <h2 class="text-center">Dashboard</h2>
    <div class="dashboard">
<?php foreach ($games as $game) { ?>
        <div class="game">
            <a href="Game/<?php echo $game[0] ?>">
                <div class="game-data">
                    <div><?php echo $game[1] ?></div>
                    <div><?php echo $game[2] ?> Wins / <?php echo $game[3] ?> Losses</div>
                    <div><?php echo $game[4] ?></div>
                </div>
            </a>
        </div>
<?php } ?>
    </div>
</body>
</html>
