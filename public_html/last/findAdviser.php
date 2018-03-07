<?php
$connection = connectToDb();
$result = sqlQuery($connection);
$response = parseResult($result);
echo json_encode($response);

function connectToDb(){
    $servername = "localhost";
    $dbUsername = "id554796_leopard";
    $dbPassword = "m.emami1391";
    $dbName = "id554796_database1";
    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);
    if ($conn->connect_error){
        $response['success'] = false;
        $response['message'] = "connect to database error";
        die(json_encode($response));
    }
    return $conn;
}

function parseResult($result){
    $response['success'] = true;
    $response['message'] = "success";
    $response['advisers'] = array();
    $num = 0;
    while ($row = $result->fetch_assoc()){
        $adviser = rowToAdviser($row);
        array_push($response['advisers'], $adviser);
        $num++;
        if ($num > 10)
            break;
    }
    return $response;
}

function rowToAdviser($row){
    $adviser['name'] = $row['name'];
    $adviser['username'] = $row['username'];
    $adviser['email'] = $row['email'];
    $adviser['familyName'] = $row['familyName'];
    $adviser['id'] = $row['id'];
    return $adviser;
}

function sqlQuery($connection){
    $result = "before sql query";
    try{
        if (isset($_POST['SEARCH'])){
            $search = $_POST['SEARCH'];
            $sql = "SELECT * FROM Advisors WHERE name LIKE '$search%' OR familyName LIKE '$search%'OR username LIKE '$search%' LIMIT 10";
            $result = $connection->query($sql);
            return $result;
        }
        else{
            $response['success'] = false;
            $response['message'] = "not set";
            die(json_encode($response));
        }
    }catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = $e->getMessage();
        die(json_encode($response));
    }
    return $result;
}