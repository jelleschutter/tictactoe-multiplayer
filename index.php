<?php

    include "config.php";

    if (isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] == true) {
        header("Location: Dashboard");
    } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
        $userData = $db_connection->query("SELECT * FROM `user` WHERE `username` = '" . htmlspecialchars($_POST["username"]) . "'");
        if ($userData->num_rows > 0) {
            $user = $userData->fetch_assoc();
            if (password_verify(htmlspecialchars($_POST["password"]), $user["password"])) {
                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["loggedIn"] = true;
                header("Location: Dashboard");
                exit;
            } else {
                $errorMessage = "Username/Password wrong!";
            }
        } else {
            $errorMessage = "User does not exist!";
        }
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
</head>
<body>
    <div class="content">
        <h2 class="text-center">Login</h2>
        <?php if (isset($errorMessage)) { ?>
        <p>
            <? echo $errorMessage;?>
        </p>
        <?php } ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" autocomplete="nickname" />
            <input type="password" name="password" placeholder="Password" />
            <button>Login</button>
        </form>
        <hr />
        <a href="Register" class="btn text-center">Sign Up</a>
    </div>
</body>
</html>
