<?php

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $page = $_GET['page'];
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
<link href="https://fonts.googleapis.com/css?family=Dancing+Script&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/gallery.css">
</head>
<body>
    <div id="nav-bar">
        <div><a href="photo_booth.php">Photo Booth</a></div>
        <div id="logout-container"><a href="logout.php">logout</a></div>
    </div>
    <h3>Photo Gallery</h3>
    <div id="wrapper">
        <?php
        // $list = scandir('img/createdImg/');
        // $list = array_reverse($list, true);
        // $list = array_diff($list, [".", ".."]);
        require_once "config/setup.php";
        $sql = "SELECT * FROM photos ORDER BY id DESC";
        if ($stmt = $pdo->prepare($sql)) {
            if ($stmt->execute()) {
                $res = $stmt->fetchAll();
                $count = 0;
                foreach ($res as $row) {
                    $count++;    
                }
                $number_of_page = round($count / 6) + 1;
                
                $pagination = array();
                for ($i = 1 ; $i <= $number_of_page; $i++) {
                    $pagination[$i] =  "<a href='gallery.php?page=$i'>$i</a>";
                }

                $start = 1;
                $end = 6;
                // echo $page;
                while (!($start <= $page && $page < $end)) {
                    $start += 5;
                    $end += 5;
                }

                $first = ($page - 1) * 6 ;
                $last = $first + 6;
                $i = 0;
                foreach ($res as $row) {
                    if ($first <= $i && $i <$last) {
                        echo '<div class="photo">';
                        echo "<a href='comment.php?filename=$row[filename]&postid=$row[id]'>";
                        echo '<img src="img/createdImg/'.$row["filename"].'" />';
                        echo "</a>";
                        echo '</div>';
                    }
                    $i++;
                }

                
                
                
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }   
        }
        ?>
   </div>

   <?php
    echo "<div>";
    if ($page < 6) {
        echo "<a>&laquo;</a>";
    } else {
        $prev = $start - 1;
        echo "<a href='gallery.php?page=$prev'>&laquo;</a>";
    }
    for ($i = $start; $i < $end; $i++) {
        if (isset($pagination[$i]))
            echo $pagination[$i];
    }
    if ($end <= $number_of_page) {
        $next = $end;
        echo "<a href='gallery.php?page=$next'>&raquo;</a>";                    
    } else {
        echo "<a>&raquo;</a>";
    }
    echo "</div>";
   ?>
</body>
</html>