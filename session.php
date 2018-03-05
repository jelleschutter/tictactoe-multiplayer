<?php

    if(isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] == true) {

    } else {
        if (isset($GameID)) {
            header("Location: ../");
        } else {
            header("Location: ./");
        }

        exit;
    }

?>
