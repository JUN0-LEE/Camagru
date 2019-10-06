<?php
    $filename =  $_GET["filename"];
    $postid = $_GET["postid"];
    $comment_err = "";
    if (!isset($_SESSION))
        session_start();
?>
<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // echo "Hello";
        if (!isset($_SESSION))
            session_start();

        if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
            header("location: login.php");
            exit;
        }


        if (!isset($_POST["commentid"]) && !isset($_POST["like"])) {
            if (empty(trim($_POST["body"]))) {
                $comment_err = "Please write comment";       
            }
    
            if (empty($comment_err)) {
                // $username = $_SESSION["username"];
                $comment = trim($_POST["body"]);
                require_once 'config/setup.php';
                $sql = "INSERT INTO comments (username, postid, userid, body) VALUES (:username, :postid, :userid, :body)";
                if ($stmt = $pdo->prepare($sql)) {
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $stmt->bindParam(":postid", $param_postid, PDO::PARAM_INT);
                    $stmt->bindParam(":userid", $param_userid, PDO::PARAM_INT);
                    $stmt->bindParam(":body", $param_body, PDO::PARAM_STR);
                    $param_username = $_SESSION["username"];
                    $param_postid = $postid;
                    $param_userid = $_SESSION["id"];
                    $param_body = $comment;
                    if ($stmt->execute()) {
                        $sql = "SELECT * FROM photos WHERE id = :postid";
                        if ($stmt = $pdo->prepare($sql)) {
                            $stmt->bindParam(":postid", $param_postid, PDO::PARAM_INT);
                            $param_postid = $postid;
                            if ($stmt->execute()) {
                                if ($row = $stmt->fetch()) {
                                    $userid = $row['userid'];
                                    $sql = "SELECT * FROM users WHERE id = :userid";
                                    if ($stmt = $pdo->prepare($sql)) {
                                        $stmt->bindParam(":userid", $param_userid, PDO::PARAM_INT);
                                        $param_userid = $userid;
                                        if ($stmt->execute()) {
                                            if ($row = $stmt->fetch()) {
                                                $notification = $row['notification'];
                                                $email = $row['email'];
                                                if ($notification == 1) {
                                                    include 'email.php';
                                                    comment_email($email, $_SESSION["username"]);
                                                }
                                            }
                                        }else {
                                            echo "Something went wrong! (select user)";
                                        }
                                    }
                                }
                            }else {
                                echo "Something went wrong! (select photo)";
                            }
                        }
                    } else {
                        echo "Something went wrong! Please try again later.(insert comment)";
                    }
                }
                unset ($stmt);
            }
        } else if (isset($_POST["commentid"]) && !isset($_POST["like"])){
            require_once 'config/setup.php';
            $commentid = $_POST["commentid"];
            $sql = "DELETE FROM comments WHERE id = :id";
            if ($stmt = $pdo->prepare($sql)) {
                $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
                $param_id = $commentid;
                if ($stmt->execute()) {

                } else {
                    echo "Something went wrong! Please try again later.";
                }
            }
        } else {
            require_once 'config/setup.php';
            $sql = "SELECT * FROM likes WHERE userid = :userid AND postid = :postid";
            if ($stmt = $pdo->prepare($sql)) {
                $stmt->bindParam(":userid", $param_userid, PDO::PARAM_INT);
                $stmt->bindParam(":postid", $param_postid, PDO::PARAM_INT);
                $param_userid = $_SESSION["id"];
                $param_postid = $postid;
                if ($stmt->execute()) {
                    if ($stmt->rowCount() == 0) {
                        $sql = "INSERT INTO likes (postid, userid, status) VALUES (:postid, :userid, 1)";
                        if ($stmt = $pdo->prepare($sql)) {
                            $stmt->bindParam(":userid", $param_userid, PDO::PARAM_INT);
                            $stmt->bindParam(":postid", $param_postid, PDO::PARAM_INT);
                            $param_userid = $_SESSION["id"];
                            $param_postid = $postid;
                            if ($stmt->execute()) {
                            }  else {
                                echo "Something went wrong! (insert like)";
                            }                      
                        }
                    } else {
                        if ($row = $stmt->fetch()) {
                            if ($row['status'] == 0) {
                                $sql = "UPDATE likes SET status = 1 WHERE postid = :postid AND userid = :userid";
                                if ($stmt = $pdo->prepare($sql)) {
                                    $stmt->bindParam(":userid", $param_userid, PDO::PARAM_INT);
                                    $stmt->bindParam(":postid", $param_postid, PDO::PARAM_INT);
                                    $param_userid = $_SESSION["id"];
                                    $param_postid = $postid;
                                    if ($stmt->execute()) {

                                    }else {
                                        echo "Something went wrong! (likes status update)";
                                    }
                                }
                            } else {
                                $sql = "UPDATE likes SET status = 0 WHERE postid = :postid AND userid = :userid";
                                if ($stmt = $pdo->prepare($sql)) {
                                    $stmt->bindParam(":userid", $param_userid, PDO::PARAM_INT);
                                    $stmt->bindParam(":postid", $param_postid, PDO::PARAM_INT);
                                    $param_userid = $_SESSION["id"];
                                    $param_postid = $postid;
                                    if ($stmt->execute()) {

                                    }else {
                                        echo "Something went wrong! (likes status update)";
                                    }
                                }
                            }
                        }
                    }
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
    <title>Comment & likes</title>
    <style>
    #container {
        display:flex;
        flex-direction: column;
        align-items:flex-start;
    }
    #comment {
        display: flex;
    }
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
#log-out-container {
  margin-right: 10px;
  cursor: pointer;
}
#photo-booth-container {
  margin-right: 10px;
  cursor: pointer;
}
#my-library-container {
  margin-right: 10px;
}
#comment-likes-container {
    width: 400px;
    display:flex;
    justify-content:space-between;
    align-items: center;
}
#likes-container {
    display:flex;

}
    </style>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
    <div id="nav">
        <div id="logo-container"><a href="/gallery.php?page=1">Go back</a></div>
        <div id="gallery-log-out-container">
            <div id="my-library-container"><a href="library.php">My Library</a></div>
            <div id="photo-booth-container"><a href="photo_booth.php">Photo Booth</a></div>
            <?php 
            if (isset($_SESSION) && isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true)
                echo "<div id='log-out-container'><a href='logout.php' >Log out</a></div>";
            else {
                echo "<div id='log-out-container'><a href='login.php' >Log in</a></div>";
            }    
            ?>
        </div>
    </div>
    <div id="container">
    <?php
    echo "<img src='img/createdImg/$filename'/>";
    ?>
    <div id="comment-likes-container">
    <h3>Comment</h3>
    <div id="likes-container">
    <?php
        require_once 'config/setup.php';
        $sql = "SELECT * FROM likes WHERE postid = :postid";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":postid", $param_postid, PDO::PARAM_INT);
            $param_postid = $postid;
            if ($stmt->execute()) {
                $like_count = 0;
                while ($row = $stmt->fetch()) {
                    if ($row["status"] == 1)
                        $like_count += 1;
                }
            } else {
                echo "Something went wrong! (like count)";
            }
        }
        echo "<span>likes : ".$like_count."</span>";
        echo "<form method='post' action='comment.php?filename=$filename&postid=$postid'>
                <input type='hidden' name='like' value=''>";
        echo "<button type='submit'>";

        $sql = "SELECT * FROM likes WHERE userid = :userid AND postid = :postid";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":userid", $param_userid, PDO::PARAM_INT);            
            $stmt->bindParam(":postid", $param_postid, PDO::PARAM_INT);
            $param_userid = $_SESSION["id"];
            $param_postid = $postid;
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 0) {
                echo "<span class='glyphicon glyphicon-heart-empty'></span>";
                }else {
                    if($row = $stmt->fetch()) {
                        if ($row['status'] == 0) {
                            echo "<span class='glyphicon glyphicon-heart-empty'></span>";
                        }else {
                            echo "<span class='glyphicon glyphicon-heart'></span>";
                        }
                    }
                }
            }
        }

        echo "</button>
        </form>"
    ?>
    </div>
    </div>
    <?php
        require_once 'config/setup.php';
        $sql = "SELECT * FROM comments WHERE postid = :postid";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":postid", $param_postid, PDO::PARAM_INT);
            $param_postid = $postid;
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    while ($row = $stmt->fetch()) {
                        echo "<div id='comment'>
                        <span>".$row["username"].":</span>
                        <span>".$row["body"]."</span>";
                        if (isset($_SESSION) && isset($_SESSION["username"])) {
                            if ($row["username"] == $_SESSION["username"]) {
                                echo "<form method='post' action='comment.php?filename=$filename&postid=$postid'>
                                        <input type='hidden' name='commentid' value='$row[id]'>
                                        <input type='submit' value='delete'>
                                    </form>";
                            }
                        }
                        
                        echo "</div>";
                    }
                } else {
                    echo "No comment yet";
                }
            }
        }
    ?>
    <?php
    echo "<form action='comment.php?filename=$filename&postid=$postid' method='post'>
            <label>Comment: </label>
            <input type='text' name='body'>
            <input type='submit'/>
        </form>";
    ?>
    <span><?php echo $comment_err; ?></span>
    </div>
</body>
</html>