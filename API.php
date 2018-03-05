<?php

    include "config.php";
    include "session.php";

    function getDB() {
        include "config.php";
        return $db_connection;
    }

    function getGame() {
        $db = getDB();
        $sql = "SELECT * FROM game WHERE game_id='" . htmlspecialchars($_POST["game"]) . "'";
        $query = $db->query($sql);

        if ($query->num_rows > 0) {
            $game = $query->fetch_assoc();
        } else {
            $response->code = 404;
            $response->text = "Not Found";
            die(json_encode($response));
        }

        return $game;
    }

    function getState() {
        $game = getGame();
        return intval($game["state"]);
    }

    function getField() {
        $game = getGame();
        $field = explode(";", $game["field"]);
        for($x = 0; $x < count($field); $x++) {
            $field[$x] = explode(",", $field[$x]);
            for($y = 0; $y < count($field[$x]); $y++) {
                $field[$x][$y] = intval($field[$x][$y]);
            }
        }
        return $field;
    }

    function getContent() {
        $field = getField();
        $content = $field[intval(htmlspecialchars($_POST["fieldrow"])) - 1][intval(htmlspecialchars($_POST["fieldcol"])) - 1];
        return $content;
    }

    function getOpponent() {
        $game = getGame();
        $db = getDB();
        $sql = "SELECT opponent.user_id, user.username FROM opponent INNER JOIN user ON opponent.user_id = user.user_id WHERE game_id = '" . $game["game_id"] . "' AND NOT opponent.user_id = " . $_SESSION["user_id"] . "";
        $opponentData = $db->query($sql);

        $opponent = $opponentData->fetch_assoc();
        return $opponent;
    }

    function getPlayerNr() {
        $game = getGame();
        $db = getDB();
        $sql = "SELECT playernr FROM opponent WHERE game_id = '" . $game["game_id"] . "' AND user_id = " . $_SESSION["user_id"] . "";
        $playerData = $db->query($sql);
        $player = $playerData->fetch_assoc();
        return intval($player["playernr"]);
    }

    function getTurn() {
        $game = getGame();
        if($game["turn"] == $_SESSION["user_id"]) {
            return true;
        } else {
            return false;
        }
    }

    function fieldToString($field) {
        $arrRows = array();
        foreach ($field as $arrRow) {
            array_push($arrRows, implode(",", $arrRow));
        }
        $strField = implode(";", $arrRows);
        return $strField;
    }

    switch (pathinfo($_SERVER["REQUEST_URI"], PATHINFO_BASENAME)) {

        case "Move":

            $game = getGame();

            if (getTurn()) {

                if (getContent() == 0) {

                    $field = getField();

                    $field[intval($_POST["fieldrow"]) - 1][intval($_POST["fieldcol"]) - 1] = getPlayerNr();

                    $opponent = getOpponent();

                    $turn = $opponent["user_id"];
                    $move = intval($game["move"]) + 1;

                    $newField = fieldToString($field);

                    if(winChecker($field)) {

                        $response->code = 1000;
                        $response->text = "You have won!";

                        $sql = "INSERT INTO win (game_id, user_id) VALUES ('" . $game["game_id"] . "', " . $_SESSION["user_id"] . ")";
                        $db_connection->query($sql);

                        $sql = "UPDATE game SET field = '" . $newField . "', state = 2, turn = '" . $turn . "', move = " . $move . " WHERE game_id = '" . $game["game_id"] . "'";

                    } elseif (drawChecker($field)) {

                        $response->code = 999;
                        $response->text = "Draw!";

                        $sql = "INSERT INTO win (game_id) VALUES ('" . $game["game_id"] . "')";
                        $db_connection->query($sql);

                        $sql = "UPDATE game SET field = '" . $newField . "', state = 2, turn = '" . $turn . "', move = " . $move . " WHERE game_id = '" . $game["game_id"] . "'";

                    } else {

                        $response->code = 11;
                        $response->text = "Moved!";

                        $sql = "UPDATE game SET field = '" . $newField . "', turn = '" . $turn . "', move = " . $move . " WHERE game_id = '" . $game["game_id"] . "'";
                    }

                    $db_connection->query($sql);

                    echo json_encode($response);


                } else {

                    $response->code = 22;
                    $response->text = "Field is already taken!";

                    echo json_encode($response);

                }

            } else {

                $response->code = 21;
                $response->text = "It's not your turn!";

                echo json_encode($response);

            }

        break;





        case "Create":

            $response->code = 200;
            $response->text = "Created!";

            echo json_encode($response);

        break;





        case "Turn":

            if(getTurn()) {
                $text = "It's your turn!";
            } else {
                $text = "It's not your turn!";
            }

            $response->code = 200;
            $response->text = $text;
            $response->turn = getTurn();

            echo json_encode($response);

        break;





        case "State":

            $response->code = 200;
            $response->state = getState();

            echo json_encode($response);

        break;





        case "Field":

            $response->code = 200;
            $response->field = getField();

            echo json_encode($response);

        break;





        case "Winner":

            $sql = "SELECT MAX(win_id) AS win_id FROM win WHERE game_id='" . htmlspecialchars($_POST["game"]) . "'";
            $query = $db_connection->query($sql);

            if ($query->num_rows > 0) {

                $win = $query->fetch_assoc();

                $sql = "SELECT win.user_id, user.username FROM win INNER JOIN user ON win.user_id = user.user_id WHERE win_id='" . $win["win_id"] . "'";
                $query = $db_connection->query($sql);
                $winner = $query->fetch_assoc();

                if (is_null($winner["user_id"])) {

                    $response->code = 200;
                    $response->type = 2;
                    $response->text = "Draw!";

                } else {

                    $sql = "SELECT playernr FROM opponent WHERE game_id = '" . htmlspecialchars($_POST["game"]) . "' AND user_id = " . $winner["user_id"] . "";
                    $playerData = $db_connection->query($sql);
                    $player = $playerData->fetch_assoc();

                    $response->code = 200;
                    $response->type = 1;
                    $response->playernr = intval($player["playernr"]);
                    $response->username = $winner["username"];

                }

                echo json_encode($response);

            } else {

                $response->code = 404;
                $response->username = "Not Found";

                echo json_encode($response);

            }

        break;





        case "Restart":
            if (getState() == 2) {
                $game = getGame();

                $turnPlayerNR = rand(1,2);

                $sql = "SELECT user_id FROM opponent WHERE game_id = '" . $game["game_id"] . "' AND playernr = " . $turnPlayerNR . "";
                $playerData = $db_connection->query($sql);
                $player = $playerData->fetch_assoc();

                $turn = $player["user_id"];

                $sql = "UPDATE game SET field = '0,0,0;0,0,0;0,0,0', state = 1, turn = '" . $turn . "', move = 0 WHERE game_id = '" . $game["game_id"] . "'";
                $db_connection->query($sql);

                $response->code = 200;
                $response->turn = ($turn == $_SESSION["user_id"]);
            } else {
                $response->code = 200;
                $response->turn = getTurn();
            }
            echo json_encode($response);

        break;





        case "Players":

            if (!empty($_POST["input"])) {

                $sql = "SELECT user_id, username FROM user WHERE username LIKE '%" . htmlspecialchars($_POST["input"]) . "%' LIMIT 5";
                $playerData = $db_connection->query($sql);

                $players = array();

                while ($player = $playerData->fetch_assoc()) {
                    if($player["user_id"] != $_SESSION["user_id"]) {
                        array_push($players, $player["username"]);
                    }
                }

                $response->code = 200;
                $response->players = $players;

            } else {
                $response->code = 200;
                $response->players = array();
            }

            echo json_encode($response);

        break;





        case "Opponents":

            $game = getGame();


            $sql = "SELECT user.username, opponent.playernr FROM opponent INNER JOIN user ON opponent.user_id = user.user_id WHERE opponent.game_id = '" . $game["game_id"] . "'";
            $playerData = $db_connection->query($sql);

            $players = array();

            while ($player = $playerData->fetch_assoc()) {
                if($player["user_id"] != $_SESSION["user_id"]) {
                    array_push($players, $player["username"]);
                }
            }

            $response->code = 200;
            $response->players = $players;

            echo json_encode($response);

        break;

    }




    function winChecker($field) {

        for ($i = 0; $i < 3; $i++) {
            if((($field[$i][0] == $field[$i][1]) && ($field[$i][0] == $field[$i][2]) && ($field[$i][0] != 0)) || (($field[0][$i] == $field[1][$i]) && ($field[0][$i] == $field[2][$i]) && ($field[0][$i] != 0))) {
                return true;
            }
        }

        if(((($field[0][0] == $field[1][1]) && ($field[0][0] == $field[2][2])) || (($field[0][2] == $field[1][1]) && ($field[0][2] == $field[2][0]))) && ($field[1][1] != 0)) {
            return true;
        }

        return false;

    }

    function drawChecker($field) {

        for ($y = 0; $y < 3; $y++) {
            for ($x = 0; $x < 3; $x++) {
                if($field[$y][$x] == 0) {
                    return false;
                }
            }
        }

        return true;

    }
?>
