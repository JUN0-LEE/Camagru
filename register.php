<?php

require_once "config/setup.php";

$username = $password = $confirm_password = $email = "";
$username_err = $password_err = $confirm_password_err = $email_err = "";

// echo "Hello";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // echo "SSSSSSSS";
    // print_r($_POST);
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $sql = "SELECT id FROM users WHERE username =:username";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = trim($_POST["username"]);
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        unset($stmt);
    }



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



    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter a email.";
    } else if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email.";
    } else {
        $sql = "SELECT id FROM users WHERE email = :email";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $param_email = trim($_POST["email"]);
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $email_err = "This email address is already in use.";
                } else {
                    $email = trim($_POST["email"]);
                }
            }else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        } 
    }

    if (empty($username_err) && empty($password_err)
    && empty($confirm_password_err) && empty($email_err)) {
        $sql = "INSERT INTO users (username, password, email, hash) VALUES (:username, :password, :email, :hash)";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":hash", $param_hash, PDO::PARAM_STR);

            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_email = $email;
            $param_hash = $hash = md5(rand(0, 1000));

            if ($stmt->execute()) {
                include 'email.php';
                send_email($email, $hash);
                header("location: confirm.php");
            } else {
                echo "Something went wrong. Please try again later.";
            }
        }
        unset($stmt);
    }
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Sign up</title>
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
      <h3>Sign Up</h3>
      <p>Please fill this form to create an account.</p>
      <form
        method="post"
        action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
        style="display:flex; flex-direction:column; align-items: center"
      >
        <label>Username</label>
        <input type="text" name="username" />
        <span style="color:red;"><?php echo $username_err;?></span>
        <label>Password</label>
        <input type="password" name="password" />
        <span style="color:red;"><?php echo $password_err;?></span>
        <label>Confirm password</label>
        <input type="password" name="confirm_password" />
        <span style="color:red;"><?php echo $confirm_password_err; ?></span>
        <label>Email</label>
        <input type="email" name="email" />
        <span style="color:red;"><?php echo $email_err; ?></span>
        <input type="submit" value="Sign Up" />
        Already have an account? <a href="login.php">Login</a>
      </form>
    </div>
  </body>
</html>