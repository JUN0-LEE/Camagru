<?php
    session_start();

    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
        header("location: index.php");
        exit;
    }
    require_once "config/setup.php";
    $username = $password = "";
    $username_err = $password_err = $active_err = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty(trim($_POST["username"]))) {
            $username_err = "Please enter username.";
        } else {
            $username = trim($_POST["username"]);
        }
        if (empty(trim($_POST["password"]))) {
            $password_err = "Please enter your password.";
        } else {
            $password = trim($_POST["password"]);
        }

        if (empty($username_err) && empty($password_err)) {
            $sql = "SELECT id, username, password, active FROM users WHERE username = :username";
            if ($stmt = $pdo->prepare($sql)) {
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = trim($_POST["username"]);
                if ($stmt->execute()) {
                    if ($stmt->rowCount() == 1) {
                        if ($row = $stmt->fetch()) {
                            $active = $row["active"];
                            $id = $row["id"];
                            $username = $row["username"];
                            $hashed_password = $row["password"];
                            if ($active && password_verify($password, $hashed_password)) {
                                session_start();
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;
                                header("location: index.php");
                            } else if (!$active && password_verify($password, $hashed_password)) {
                                $active_err = "Please make sure that you activated your account.";
                            } else {
                                $password_err = "The password you entered was not valid.";
                            }
                        }
                    } else {
                        $username_err = "No account found with that username.";
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Document</title>
    <style>
      #wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
      }
    </style>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  </head>
  <body>
    <div id="wrapper">
      <h1>Camagru</h1>
      <h3>Login</h3>
      <form
        method="post"
        action="login.php"
        style="display:flex; flex-direction:column; align-items: center"
      >
        <label>Username</label>
        <input type="text" name="username" />
        <span><?php echo $username_err; ?></span>
        <label>Password</label>
        <input type="password" name="password" />
        <span><?php echo $password_err; ?></span>
        <span><?php echo $active_err;?> </span>
        <input type="submit" value="Login" />
        Don't have an account? <a href="register.php">Sign up</a><br/>
        Forgot your password? <a href="reset_pw.php">Reset password</a>
      </form>
    </div>
  </body>
</html>