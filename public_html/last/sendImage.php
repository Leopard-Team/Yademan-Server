<?php
if (isset($_POST['USERNAME']) && isset($_POST['TYPE'])){
    $filePath = "../images/".$_POST['TYPE']."_".$_POST['USERNAME'].".jpg";
    if (file_exists($filePath)){
        $imageData = file_get_contents($filePath);
        echo base64_encode($imageData);
    }else{
        echo "noFile";
    }
}