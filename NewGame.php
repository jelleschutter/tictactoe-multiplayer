<?php

    include "config.php";
    include "session.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST["opponent"])) {
            $userData = $db_connection->query("SELECT * FROM `user` WHERE `username` = '" . htmlspecialchars($_POST["opponent"]) . "'");

            if ($userData->num_rows > 0) {
                $user = $userData->fetch_assoc();

                $alreadExists = true;

                do {
                    $newGame = generateRandomString();
                    $sql = "SELECT game_id FROM game WHERE game_id = '" . $newGame . "'";
                    $games = $db_connection->query($sql);
                    $alreadExists = ($games->num_rows > 0);
                } while ($alreadExists);

                $sql = "INSERT INTO opponent (game_id, user_id, playernr) VALUES ('" . $newGame . "', " . $_SESSION["user_id"] . ", 1)";
                $db_connection->query($sql);

                $sql = "INSERT INTO opponent (game_id, user_id, playernr) VALUES ('" . $newGame . "', " . $user["user_id"] . ", 2)";
                $db_connection->query($sql);

                $turnPlayerNR = rand(1,2);

                $sql = "SELECT user_id FROM opponent WHERE game_id = '" . $newGame . "' AND playernr = " . $turnPlayerNR . "";
                $playerData = $db_connection->query($sql);
                $player = $playerData->fetch_assoc();

                $turn = $player["user_id"];

                $sql = "INSERT INTO game (game_id, turn) VALUES ('" . $newGame . "', " . $turn . ")";
                $db_connection->query($sql);

                header("Location: Game/" . $newGame ."");
                exit;
            } else {
                $errorMessage = "Opponent does not exist!";
            }
        } else {
            $errorMessage = "No Opponent chosen!";
        }
    }

    // Copied from https://stackoverflow.com/questions/4356289/php-random-string-generator

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title></title>
    <link href="../Style.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#opponent").on("input", function () {
                $.post("API/Players",
                {
                    input: $(this).val()
                },
                function (data, status) {
                    var playersData = JSON.parse(data);
                    var players = playersData.players;
                    var newHTML = "";

                    for(i = 0; i < players.length; i++) {
                        newHTML += "<div class='user'>" + players[i] + "</div>";
                    }

                    $("#complete").html(newHTML);

                    $(".user").on("click", function () {
                        $("#opponent").val($(this).text());
                    });
                });
            });
        });
    </script>
</head>
<body>
    <header>
        <a href="Dashboard">Back</a>
        <a href="Logout">Logout</a>
    </header>
    <div class="content">
        <h2 class="text-center">New Game</h2>
        <?php if (isset($errorMessage)) { ?>
        <p>
            <? echo $errorMessage;?>
        </p>
        <?php } ?>
        <form method="post">
            <input type="text" class="text-center" id="opponent" name="opponent" placeholder="Opponent Username" autocomplete="off"/>
            <div id="complete"></div>
            <button>Create Game</button>
        </form>
    </div>
</body>
</html>
