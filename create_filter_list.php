<?php 
$list = scandir('img/filter/');
foreach ($list as $img_name) {
    if ($img_name[0] !== '.') {
        echo '<img
        src="img/filter/'.$img_name.'" 
        onclick="store_src(this.src)" 
        onmouseover="" style="cursor: 
        pointer;" 
        class="image-to-merge"/>';
    }
}
?>