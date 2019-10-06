<?php
    require_once "config/setup.php";
    $reset = 0;
    $err = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = trim($_POST["email"]);
        $sql = "UPDATE users SET password = :password WHERE email = :email";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $password = md5(rand(0, 1000));
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_email = $email;

            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    include 'email.php';
                    reset_email($email, $password);
                    $reset = 1;
                    // session_destroy();
                } else {
                    $err = "Please check your email address. We don't have that email in our database.";
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
    <title>Reset password</title>
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
        if ($reset) {
            echo "Password reset email has sent to your email address.";
        } else {
            echo "Something went wrong. Please try again later";
            echo $err;
        }
    ?>
</body>
</html>