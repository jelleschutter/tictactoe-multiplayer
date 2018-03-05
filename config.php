<?php

    session_start();

    $db->hostname = "localhost";
    $db->database = "ttt";
    $db->username = "ttt";
    $db->password = "";

    $db_connection = new mysqli($db->hostname, $db->username, $db->password, $db->database);

?>
