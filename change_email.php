<?php
    session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    }

?>

<?php
    $change = 0;
    $email_err = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (!empty(trim($_POST["email"]))) {
            require_once "config/setup.php";

            $sql = "SELECT id FROM users WHERE email = :email";
            if ($stmt = $pdo->prepare($sql)) {
                $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
                $param_email = $email = trim($_POST["email"]);
                if ($stmt->execute()) {
                    if ($stmt->rowCount() == 1) {
                        $email_err = "This email is alread taken.";
                    } else {
                        $sql = "UPDATE users SET email = :email WHERE id = :id";
                        if ($stmt = $pdo->prepare($sql)) {
                            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
                            $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
                            $param_email = $email;
                            $param_id = $_SESSION["id"];
                            if ($stmt->execute()) {
                                $change = 1;
                            } else {
                                $email_err = "Something went wrong! Please try again later.";
                            }
                        }
                    }
                } else {
                    $email_err = "Something went wrong! Please try again later.";
                }
            }
        } else {
            $email_err = "Please enter an email.";
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
            echo "<p>Please enter the new email address.</p><br>";
            echo '<form action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="post">
                    <input type="email" name="email">
                    <input type="submit">
                </form>';
        }
        else {
            echo "Hello, $_SESSION[username]";
            echo "<p>Your email has been changed successfully.</p><br>";
            echo "<a href='profile.php'>profile</a>";
        }
    ?>
    <span><?php echo $email_err; ?> </span>
</body>
</html>