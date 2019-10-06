<?php
require_once "config/setup.php";

if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash'])){
    $email = trim($_GET['email']);
    $hash = trim($_GET['hash']);
    // echo $email."<br/>";
    // echo $hash."<br/>";

    $sql = "SELECT email, hash, active FROM users WHERE email = :email AND hash = :hash AND active = 0";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
        $stmt->bindParam(":hash", $param_hash, PDO::PARAM_STR);
        // $stmt->bindParam(":active", $param_active, PDO::PARAM_STR);        
        $param_email = $email;
        $param_hash = $hash;
        // $param_active = 0;

        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                $sql = "UPDATE users SET active = 1 WHERE email = :email AND hash = :hash AND active = 0";
                    if ($stmt = $pdo->prepare($sql)) {
                        $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
                        $stmt->bindParam(":hash", $param_hash, PDO::PARAM_STR);
                        $param_email = $email;
                        $param_hash = $hash;
                        if ($stmt->execute()) {
                            echo "Your account has been activated, you can now login";
                            echo "<a href='login.php'>login</a>";
                        }
                    }
            } else {
                echo "The url is either invalid or you already have activated your account.";
                echo "<a href='login.php'>login</a>";
            }
        }
    }
} else {
    echo "Invalid aproach, please use the link that has been send to your email or ";
    echo "<a href='login.php'>login</a>";
}
?>