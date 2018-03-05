<?php

    include "config.php";

    $GameID = pathinfo($_SERVER['REQUEST_URI'], PATHINFO_BASENAME);

    include "session.php";

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TTT - <?php echo $GameID; ?></title>
    <link href="../Style.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="../GameScript.js"></script>
</head>
<body>
    <header>
        <a href="../Dashboard">Back</a>
        <a href="../Logout">Logout</a>
    </header>
    <div class="content field">
        <div class="row" data-fieldrow="1">
            <div class="box" data-fieldcol="1">
            </div>
            <div class="box" data-fieldcol="2">
            </div>
            <div class="box" data-fieldcol="3">
            </div>
        </div>
        <div class="row" data-fieldrow="2">
            <div class="box" data-fieldcol="1">
            </div>
            <div class="box" data-fieldcol="2">
            </div>
            <div class="box" data-fieldcol="3">
            </div>
        </div>
        <div class="row" data-fieldrow="3">
            <div class="box" data-fieldcol="1">
            </div>
            <div class="box" data-fieldcol="2">
            </div>
            <div class="box" data-fieldcol="3">
            </div>
        </div>
    </div>
    <div class="end-game">
        <p><span class="winner"></span> has won the game!</p>
        <button>Play again!</button>
    </div>
    <div id="snackbar"></div>
</body>
</html>
