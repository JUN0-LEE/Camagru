<?php
function update_photo_table($filename, $userid, $pdo) {
    $sql = "INSERT INTO photos (filename, userid) VALUES (:filename, :userid)";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":filename", $param_filename, PDO::PARAM_STR);
        $stmt->bindParam(":userid", $param_userid, PDO::PARAM_STR);

        $param_filename = $filename;
        $param_userid = $userid;

        if ($stmt->execute()) {
            echo "Your photo has been uploaded successfully.";
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
}

?>