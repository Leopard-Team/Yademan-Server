<?php
    $conn = databaseConnect();
    if (isset($_POST['UNAME']) && isset($_POST['TYPE'])) {
        $queryResult = sqlQuery($conn, $_POST['UNAME'], $_POST['TYPE']);
        $response = parseResult($queryResult);
    }
    else{
        if (!isset($_POST['UNAME']))
            $response['message'] = "uname";
        else
            $response['message'] = "type";
        $response['message'] = $response['message']." Not Set";
        $response['success'] = false;
        $response['noQuestions'] = true;
    }
    if ($_POST['TYPE'] == "student")
        $response['image'] = getAdviserImage($_POST['ADUNAME']);
    echo json_encode($response);
    $conn->close();

function parseResult($result){
    $response['questions'] = array();
    if ($result->num_rows > 0){
        while ($row = $result->fetch_assoc()){
            array_push($response['questions'],$row);
        }
        $response['message'] = "Success";
        $response['success'] = true;
        $response['noQuestions'] = false;
    }else{
        $response['message'] = "No Questions";
        $response['success'] = true;
        $response['noQuestions'] = true;
    }
    return $response;
}

function getAdviserImage($username){
    $filePath = "../images/ADVISER_".$username.".jpg";
    if (file_exists($filePath)) {
        $imageData = file_get_contents($filePath);
        return base64_encode($imageData);
    }
    return "noFile";
}

function sqlQuery($conn, $username, $type){
    $sql = "SELECT * FROM questions WHERE  $type = '$username' ";
    $result = $conn->query($sql);
    return $result;
}

function databaseConnect(){
    $servername = "localhost";
    $usernameDB = "id554796_leopard";
    $passwordDB = "m.emami1391";
    $dbname = "id554796_database1";
    $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);
    if ($conn -> connect_error){
        $response['message'] = $conn->connect_error;
        $response['success'] = false;
        $response['noQuestions'] = true;
        die(json_encode($response));
    }
    return $conn;
}

?>
