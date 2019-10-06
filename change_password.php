<?php
    session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    }

?>

<?php

    $change = 0;
    $password_err = $confirm_password_err =  "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty(trim($_POST["password"]))) {
            $password_err = "Please enter a password.";
        } else if(strlen(trim($_POST["password"])) < 6) {
            $password_err = "Password must have at least 6 characters.";
        } else if(!preg_match('~[0-9]+~', $_POST["password"])) {
            $password_err = "Password must be characters with numbers";
        } else {
            $password = trim($_POST["password"]);
        }
    
        if (empty(trim($_POST["confirm_password"]))) {
            $confirm_password_err = "Please confirm password.";
        } else {
            $confirm_password = trim($_POST["confirm_password"]);
            if (empty($password_err) && ($password != $confirm_password)) {
                $confirm_password_err = "Password did not match.";
            }
        }

        if (empty($password_err)) {
            require_once "config/setup.php";

            $sql = "UPDATE users SET password = :password WHERE id = :id";
            if ($stmt = $pdo->prepare($sql)) {
                $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
                $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
                $param_password = password_hash($password, PASSWORD_DEFAULT);
                $param_id = $_SESSION["id"];
                if ($stmt->execute()) {
                    $change = 1;
                } else {
                    $password_err = "Something went wrong. Please try again later.";
                }
            }
            unset ($stmt);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <title>Document</title>
</head>
<body>
    <?php
        if (!$change) {
            echo "<a href='profile.php'>go back</a><br>";
            echo "Hello, $_SESSION[username]";
            echo'<form action="change_password.php" method="post">
                    <label>New password: </label>
                    <input type="password" name="password"><br>
                    <label>Confirm password: </label>
                    <input type="password" name="confirm_password"><br>
                    <input type="submit"><br>
                </form><br>'; 
        } else {
            echo "Hello, $_SESSION[username]";
            echo "<p>Your password has successfully changed.</p><br>";
            echo "<a href='logout.php'>logout</a>";
        }
    ?>
    <span><?php echo $password_err; ?></span><br>
    <span><?php echo $confirm_password_err; ?></span>
</body>
</html>