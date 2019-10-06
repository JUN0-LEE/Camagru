<?php
    session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    }

?>

<?php

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // echo $_POST["status"];
        require_once 'config/setup.php';
        if ($_POST['status'] == "Turn off notification") {
            $sql = "UPDATE users SET notification = 0 WHERE id = :userid";
            if ($stmt = $pdo->prepare($sql)) {
                $stmt->bindParam(":userid", $param_userid, PDO::PARAM_INT);
                $param_userid = $_SESSION["id"];
                if ($stmt->execute()) {

                } else {
                    echo "Something went wrong! UPDATE notification";
                }
            }
        } else if ($_POST['status'] == "Turn on notification") {
            $sql = "UPDATE users SET notification = 1 WHERE id = :userid";
            if ($stmt = $pdo->prepare($sql)) {
                $stmt->bindParam(":userid", $param_userid, PDO::PARAM_INT);
                $param_userid = $_SESSION["id"];
                if ($stmt->execute()) {

                } else {
                    echo "Something went wrong! UPDATE notification";
                }
            }
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
    <title>Change notification settings</title>
</head>
<body>
    <a href="profile.php">go back</a><br>
    <?php

    require_once 'config/setup.php';
    $sql = "SELECT * FROM users WHERE id = :userid";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":userid", $param_userid, PDO::PARAM_INT);
        $param_userid = $_SESSION["id"];
        if ($stmt->execute()) {
            if ($row = $stmt->fetch()) {
                $status = $row['notification'];
                $value = "";
                if ($status == 1) {
                    $value = "Turn off notification";
                } else {
                    $value = "Turn on notification";
                }
            }
        } else {
            echo "Something went wrong! (SELECT users)";
        }
    }
    echo "<form method='post' action='change_notification.php'>
            <input type='submit' name='status' value='$value'>
        </form>";
    ?>
</body>
</html>