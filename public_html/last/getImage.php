<?php
$connection = databaseConnect();
saveImage();
$response['message'] = $_POST['IMAGE'];
$response['success'] = true;
echo json_encode($response);
function databaseConnect()
{
    $servername = "localhost";
    $dbUsername = "id554796_leopard";
    $dbPassword = "m.emami1391";
    $dbName = "id554796_database1";
    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);
    if ($conn->connect_error) {
        $response['message'] = $conn->connect_error;
        $response['success'] = false;
        die(json_encode($response));
    }
    return $conn;
}

function saveImage(){
    if (isset($_POST['IMAGE']) && isset($_POST['TYPE']) && isset($_POST['USERNAME'])){
        $base = $_POST['IMAGE'];
        $binary = base64_decode($base);
        $file = fopen("../images/".$_POST['TYPE']."_".$_POST['USERNAME'].".jpg", "wb");
        fwrite($file, $binary);
        fclose($file);
    }else{
        $response['message'] = "not set";
        $response['success'] = false;
        die(json_encode($response));
    }
}