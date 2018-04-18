<?php

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

    function getPlayer() {
        $game = getGame();
        $db = getDB();
        $sql = "SELECT user_id, username FROM user WHERE user_id = " . $_SESSION["user_id"] . "";
        $playerData = $db->query($sql);
        $player = $playerData->fetch_assoc();
        return $player;
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

?>
