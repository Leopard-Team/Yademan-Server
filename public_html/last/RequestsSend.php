<?php
$servername = "localhost";
$usernameDB = "id554796_leopard";
$passwordDB = "m.emami1391";
$dbname = "id554796_database1";
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);
if ($conn->connect_error){
    $response['message'] = $conn->connect_error;
    $response['success'] = false;
    die(json_encode($response));
}
try{
    if (isset($_POST['ADUNAME'])){
        $adUserName = $_POST['ADUNAME'];
        $sql = "SELECT text, student FROM Requests WHERE advisor = '$adUserName'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0){
            while ($row = $result->fetch_assoc()) {
                if (!isset($response['text'][$row['student']]))
                    $response['text'][$row['student']] = array();
                array_push($response['text'][$row['student']],$row['text']);
            }
            $i = 0;
            $keys = array_keys($response['text']);
            foreach ($keys as $key){
                $response['names'][$i] = $key;
                $i+=1;
            }
            $response['message'] = "Success";
            $response['success'] = true;
        }else{
            $response['message'] = "No requests";
            $response['success'] = true;
        }
    }
    else{
        $response['message'] = "Not Set";
        $response['success'] = false;
    }
}catch (Exception $e){
    $response['message'] = $e->getMessage();
    $response['success'] = false;
}finally{
    echo json_encode($response);
    $conn->close();
}
?>