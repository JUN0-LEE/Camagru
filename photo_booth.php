<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
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
    <title>Photo Booth</title>
    <link rel="stylesheet" type="text/css" href="css/photo_booth.css">
    <script src="js/photo_booth.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
    <div id="nav">
        <div id="logo-container"><a href="/gallery.php?page=1">Camagru</a></div>
        <div id="gallery-log-out-container">
            <div id="my-library-container"><a href="library.php">My Library</a></div>
            <div id="profile-container"><a href="profile.php">Profile</a></div>
            <div id="log-out-container"><a href="logout.php" >Log out</a></div>
        </div>
    </div>
    <div id="wrapper-0">
    <div id="tab-bar">
        <div id="upload-tab">
            <h1>Upload File</h1>
        </div>
        <div id="camera-tab">
            <h1>Camera</h1>
        </div>
    </div>
    <div id="contents-container">
        <div id="video-canvas-container">
        <div id="video-container" style="display:flex; flex-direction:column;">
            <video id="player" controls autoplay></video>
            <button id="capture">Capture</button>
        </div>
        <div id="canvas-container">
            <form method="post" accept-charset="utf-8" name="form1">
                <input name="hidden_data" id="hidden_data" type="hidden" />
                <input name="hidden_x" id="hidden_x" type="hidden" value=""/>
                <input name="hidden_y" id="hidden_y" type="hidden" value=""/>
                <input name="hidden_h" id="hidden_h" type="hidden" value=""/>
                <input name="hidden_w" id="hidden_w" type="hidden" value=""/>
                <input name="hidden_f" id="hidden_f" type="hidden" value=""/>
                <input name="face-width" id="face-width" type="hidden" value=""/>
                <input name="face-height" id="face-height" type="hidden" value=""/>
            </form>
        </div>
        </div>

        <div id="upload-img-container" style="position: relative; left:0; top:0; border: solid 1px red;">
    
            <div id="my-file">
                <img id="frame"/>
                <form method="post" accept-charset="utf-8" name="form2">
                    <input name="hidden_data2" id="hidden_data2" type="hidden" />
                </form>
                <input type="file" accept="image/*" capture="camera" id="preview" style="align-self:flex-end; justify-self:flex-end; margin-top:auto"/>                
            </div>

            <div id="place-here"></div>
        
        </div>

    <div class="scroll-button-wrapper">
    
    <div class="overflow">
        <?php include 'create_filter_list.php';?>
    </div>
    
    <div>
    
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" id="input-form"> 
    <label for="x-cor">x:</label>
    <input type="number" step="5" name="left" style="width:30px;" onchange="changeX(this.value);" value="150" id="x"/>
    </div>

    <div>
    <label for="y-cor">y:</label>
    <input type="number" step="5" name="top" style="width:30px;" onchange="changeY(this.value);" value="100" id="y"/>
    </div>

    <div>
    <label for="width">h:</label>
    <input type="number" step="5" name="height" style="width:30px;" onchange="changeH(this.value);" value="100" id="h"/>    
    </div>

    <div>
    <label for="width">w:</label>
    <input type="number" step="5" name="width" style="width:30px;" onchange="changeW(this.value);" value="100" id="w"/>
    </div>
    <input type="hidden" name="img-width" value="" id="img-width">
    <input type="hidden" name="img-height" value="" id="img-height">
    <input type="hidden" name="filter-path" value="" id="filter-path">    
    <button type="submit" onclick="get_img_infos();" id="generate-photo">Generate photo!</button>
    </form>
    
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <button type="submit">Reset</button>
    </form>
    </div>

    </div>
    <div class="photo-wrapper">
    </div>
</div>
    <script src="js/webcam.js"></script>
    <div id="tmp"></div>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // print_r($_GET);
    if (isset($_GET["left"]) && isset($_GET["top"]) && isset($_GET["width"]) 
    && isset($_GET["img-width"]) && isset($_GET["img-height"])
    && isset($_GET["filter-path"])) {
        $left = intval($_GET["left"]);
        $top = intval($_GET["top"]);
        $filter_width = intval($_GET["width"]);
        $filter_height = intval($_GET["height"]);
        $face_width = intval($_GET["img-width"]);
        $face_height = intval($_GET["img-height"]);
    }
    if (!isset($_GET["filter-path"]))
        ;
    else if (($filter_path = $_GET["filter-path"]) === "")
        echo "<script>alert('No filter has selected!');</script>";
    else {
    if (isset($_SESSION["extension"]) && isset($_SESSION["filename"])) {
        $ext = strtolower($_SESSION["extension"]);
        $filename = $_SESSION["filename"];
        if ($ext === "jpg" || $ext === "jpeg")
            $img = imagecreatefromjpeg("img/upload/".$filename);
        else if ($ext === "png")
            $img = imagecreatefrompng("img/upload/".$filename);
        
        list($width, $height) = getimagesize("img/upload/".$filename);
        $resized_face = imagecreatetruecolor($face_width, $face_height);
        imagecopyresampled($resized_face, $img, 0, 0, 0, 0, $face_width, $face_height, $width, $height);
        
        $filter_path = "img/filter/".$filter_path;
        $filter = imagecreatefrompng($filter_path);
        
        list($width, $height) = getimagesize($filter_path);
        $resized_filter = imagecreatetruecolor($face_width, $face_height);
        $color = imagecolorallocatealpha($resized_filter, 0, 0, 0, 127);
        imagefill($resized_filter,0,0,$color);
        imagesavealpha($resized_filter, true);
        // imagealphablending($resized_filter, false);
        imagecopyresampled($resized_filter, $filter, 0, 0, 0, 0, $filter_width, $filter_height, $width, $height);
        
        imagepng($resized_filter, 'img/resized.png');
    
        // $margin = (400 - $face_height)/2;

        include 'image_function.php';
        imagecopymerge_alpha($resized_face, $resized_filter, $left, $top, 0, 0, $filter_width, $filter_height, 100);
    
        date_default_timezone_set('America/Los_Angeles');
        $filename = date('m-d-Y_hisa').'.png';

        imagepng($resized_face, 'img/createdImg/'.$filename);
        // echo "Hello";
        imagedestroy($img);
        imagedestroy($resized_face);
        imagedestroy($resized_filter);
        imagedestroy($filter);

        require_once "config/setup.php";
        include 'photo_update.php';
        update_photo_table($filename, $_SESSION["id"], $pdo);

        //echo "<img src='img/createdImg/".$filename."'/>";
        unset($_SESSION["filename"]);
        unset($_SESSION["extension"]);
    } else {
        echo "<script>alert('Please upload file first!');</script>";
    }
}  
}

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {
            $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png");
            $filename = $_FILES["photo"]["name"];
            $filetype = $_FILES["photo"]["type"];
            $filesize = $_FILES["photo"]["size"];

            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if (!array_key_exists($ext, $allowed)) die("Error: Please select a valid file format.");
            $maxsize = 5 * 1024 * 1024;
            if (in_array($filetype, $allowed)) {
                move_uploaded_file($_FILES["photo"]["tmp_name"], "img/upload/".$filename);
                echo "<script>alert('You file was uploaded successfully.');</script>";
            } else {
                echo "Error: There was a problem uploading your file. Please try again.";
            }
            $_SESSION["extension"] = $ext;
            $_SESSION["filename"] = $filename;
        } 
        else if(isset($_POST['hidden_data'])) {
            $upload_dir = "img/upload/";
            $img = $_POST['hidden_data'];
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            $filename = time(). ".png";
            // $filename = $_POST['hidden_h']."zz.png";
 
            $file = $upload_dir . $filename;
            $success = file_put_contents($file, $data);
            print $success ? $file : 'Unable to save the file.';
            // $_SESSION["extension"] = "png";
            // $_SESSION["filename"] = $filename;
            // if (isset($_POST['hidden_x']) && isset($_POST['hidden_y'])
            // && isset($_POST['hidden_h']) && isset($_POST['hidden_w'])
            // && isset($_POST['hidden_f']) && isset($_POST['face-width'])
            // && isset(($_POST['face-height']))) {
                $left = intval($_POST['hidden_x']);
                $top = intval($_POST['hidden_y']);
                $filter_width = intval($_POST['hidden_w']);
                $filter_height = intval($_POST['hidden_h']);
                $face_width = intval($_POST['face-width']);
                $face_height = intval($_POST['face-height']);
                $filter_name = $_POST['hidden_f'];

        $img = imagecreatefrompng("img/upload/".$filename);                
        list($width, $height) = getimagesize("img/upload/".$filename);
        $resized_face = imagecreatetruecolor($face_width, $face_height);
        imagecopyresampled($resized_face, $img, 0, 0, 0, 0, $face_width, $face_height, $width, $height);
        
        $filter_path = "img/filter/".$filter_name;
        $filter = imagecreatefrompng($filter_path);
        
        list($width, $height) = getimagesize($filter_path);
        $resized_filter = imagecreatetruecolor($face_width, $face_height);
        $color = imagecolorallocatealpha($resized_filter, 0, 0, 0, 127);
        imagefill($resized_filter,0,0,$color);
        imagesavealpha($resized_filter, true);
        // imagealphablending($resized_filter, false);
        imagecopyresampled($resized_filter, $filter, 0, 0, 0, 0, $filter_width, $filter_height, $width, $height);
        
        imagepng($resized_filter, 'img/resized.png');
        include 'image_function.php';
        imagecopymerge_alpha($resized_face, $resized_filter, $left, $top, 0, 0, $filter_width, $filter_height, 100);
    
        date_default_timezone_set('America/Los_Angeles');
        $filename = date('m-d-Y_hisa').'.png';

        imagepng($resized_face, 'img/createdImg/'.$filename);
        // echo "Hello";
        imagedestroy($img);
        imagedestroy($resized_face);
        imagedestroy($resized_filter);
        imagedestroy($filter);

        require_once "config/setup.php";
        include 'photo_update.php';
        update_photo_table($filename, $_SESSION["id"], $pdo);

        echo "<img src='img/createdImg/".$filename."'/>";
            // }
          }
        else if (isset($_POST['hidden_data2'])) {
            $upload_dir = "img/upload/";
            $img = $_POST['hidden_data2'];
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            $filename = time().".png";
            $file = $upload_dir . $filename;
            $success = file_put_contents($file, $data);
            print $success ? $file : 'Unable to save the file.';
            $_SESSION["extension"] = "png";
            $_SESSION["filename"] = $filename;
        }
          else {
            "Error: ".$_FILES["photo"]["error"];
        }
    }
?> 