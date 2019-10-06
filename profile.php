<?php
    session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Profile</title>
    <style>
        #nav {
  height: 50px;
  background-color: aliceblue;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
#gallery-log-out-container {
  display: flex;
}
#photo-booth-container {
  margin-right: 10px;
  cursor: pointer;
}
#log-out-container {
  margin-right: 10px;
  cursor: pointer;
}#my-library-container {
  margin-right: 10px;
}
    </style>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

</head>
<body>
    <div id="nav">
        <div id="logo-container"><a href="/gallery.php?page=1">Camagru</a></div>
        <div id="gallery-log-out-container">
            <div id="my-library-container"><a href="library.php">My Library</a></div>
            <div id="photo-booth-container"><a href="photo_booth.php">Photo Booth</a></div>
            <div id="log-out-container"><a href="logout.php" >Log out</a></div>
        </div>
    </div>
    <?php

        require_once "config/setup.php";
        $sql = "SELECT * FROM users WHERE id = :userid";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":userid", $param_userid, PDO::PARAM_INT);
            $param_userid = $_SESSION["id"];
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        $username = $row["username"];
                        $email = $row["email"];
                        $status = $row['notification'];
                    }
                }
            }
        }
    ?>
    <label>username: </label><?php echo $username; ?><br/>
    <label>email: </label><?php echo $email; ?><br/>
    <label>notification: </label> <?php echo $status == 1 ? "on" : "off"; ?><br>
    <a href="change_username.php">Change username</a><br>
    <a href="change_password.php">Change password</a><br>
    <a href="change_email.php">Change email</a><br>
    <a href="change_notification.php">Change notification setting</a><br>
</body>
</html>