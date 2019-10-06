<?php
    session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    }

?>
<?php 
    $change = 0;
    $username_err = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty(trim($_POST["username"]))) {
            require_once "config/setup.php";

            $sql = "SELECT id FROM users WHERE username = :username";
            if ($stmt = $pdo->prepare($sql)) {
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $username = trim($_POST["username"]);
                $param_username = $username;
                if ($stmt->execute()) {
                    if ($stmt->rowCount() == 1) {
                        $username_err = "This username is already taken.";
                    } else {
                        $sql = "UPDATE users SET username = :username WHERE id = :id";
                        if ($stmt = $pdo->prepare($sql)) {
                            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                            $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
                            $param_username = $username;
                            $param_id = $_SESSION["id"];
                            if ($stmt->execute()) {
                                $change = 1;
                                $_SESSION["username"] = $username;
                            } else {
                            $username_err = "Something went wrong. Please try again later";
                            }
                        }
                    }
                } else {
                    $username_err = "Something went wrong. Please try again later";                    
                }
            }
            
        } else {
            $username_err = "Please put valid username.";
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
            echo "<p>Please enter the new username.</p>";
            echo '<form action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="post">
                    <input type="text" name="username">
                    <input type="submit">
                </form>';
        }
        else {
            echo "Hello, $_SESSION[username]";
            echo "<p>Your username has been changed successfully.</p><br>";
            echo "<a href='profile.php'>profile</a>";
        }
    ?>
    <span><?php echo $username_err; ?></span>
</body>
</html>