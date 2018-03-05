<?php

    include "config.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = strtolower(trim(htmlspecialchars($_POST["username"])));
        $password = trim(htmlspecialchars($_POST["password"]));
        $pattern = "([a-z0-9_](?:(?:[a-z0-9_]|(?:\.(?!\.)))*(?:[a-z0-9_]))?)";
        if(preg_match($pattern, $username)) {
            if(strlen($username) > 0 && strlen($password) >= 8) {
                $sql = "SELECT username FROM user WHERE username = '" . $username . "'";
                $usersWithSameUsername = $db_connection->query($sql);
                if ($usersWithSameUsername->num_rows == 0) {
                    $sql = "INSERT INTO `user`(`username`, `password`) VALUES ('" . $username . "', '" . password_hash($password, PASSWORD_DEFAULT) . "')";
                    $db_connection->query($sql);
                    header("Location: ./");
                } else {
                    $errorMessage = "Username already exists!";
                }
            } else {
                $errorMessage = "Username must contain at least 1 character and Password must contain at least 8 characters!";
            }
        } else {
            $errorMessage = "Only a-z, 0-9, underscore and dot allowed!";
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
        <h2 class="text-center">Register</h2>
        <?php if (isset($errorMessage)) { ?>
        <p>
            <? echo $errorMessage;?>
        </p>
        <?php } ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($_POST["username"]); ?>" autocomplete="nickname" />
            <input type="password" name="password" placeholder="Password" value="<?php echo htmlspecialchars($_POST["password"]); ?>" />
            <button>Register</button>
        </form>
    </div>
</body>
</html>
