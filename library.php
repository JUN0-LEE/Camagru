<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>


<?php 


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST) && isset($_POST['postid'])) {
            require_once 'config/setup.php';
            $postid = $_POST['postid'];
            $sql = "DELETE FROM photos WHERE id = :postid";
            if ($stmt = $pdo->prepare($sql)) {
                $stmt->bindParam(":postid", $param_postid, PDO::PARAM_INT);
                $param_postid = $postid;
                if ($stmt->execute()) {
                } else {
                    echo "Something went wrong! (while deleting post)";
                }
            }
            $sql = "DELETE FROM comments WHERE postid = :postid";
            if ($stmt = $pdo->prepare($sql)) {
                $stmt->bindParam(":postid", $param_postid, PDO::PARAM_INT);
                $param_postid = $postid;
                if ($stmt->execute()) {

                }else {
                    echo "Something went wrong! (while deleting comments)";
                }
            }
            $sql = "DELETE FROM likes WHERE postid = :postid";
            if ($stmt = $pdo->prepare($sql)) {
                $stmt->bindParam(":postid", $param_postid, PDO::PARAM_INT);
                $param_postid = $postid;
                if ($stmt->execute()) {

                }else {
                    echo "Something went wrong! (while deleting comments)";
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
    <title>Document</title>
    <link rel="stylesheet" href="css/library.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

</head>
<body>
    <div id="nav-bar">
        <div><a href="photo_booth.php">Photo Booth</a></div>
        <div id="logout-container"><a href="logout.php">logout</a></div>
    </div>
    <h3>My Library</h3>
    <div id="wrapper">
        <?php
        // $list = scandir('img/createdImg/');
        // $list = array_reverse($list, true);
        // $list = array_diff($list, [".", ".."]);
        require_once "config/setup.php";
        $sql = "SELECT * FROM photos WHERE userid = :userid ORDER BY id DESC";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":userid", $param_userid, PDO::PARAM_INT);
            $param_userid = $_SESSION["id"];
            if ($stmt->execute()) {
                $res = $stmt->fetchAll();
                foreach ($res as $row) {
                    echo '<div class="photo">';
                    echo '<img src="img/createdImg/'.$row["filename"].'" />';
                    echo "<form method='post' action='library.php'>
                            <input type='hidden' name='postid' value='$row[id]'>
                            <input type='submit' value='delete'>
                         </form>";
                    echo '</div>';    
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }   
        }
        ?>
</body>
</html>