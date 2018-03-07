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
    if(isset($_POST['REQTYPE']) && isset($_POST['ADUNAME']) && isset($_POST['STUNAME'])){
        $reqtype = $_POST['REQTYPE'];
        $advisor = $_POST['ADUNAME'];
        $student = $_POST['STUNAME'];
        $sql = "DELETE FROM Requests WHERE advisor = '$advisor' AND student = '$student'";
        if ($conn->query($sql) === true){
            if ($reqtype == "accept"){
                $sql2 = "SELECT advisor FROM androidlogin WHERE username = '$student'";
                $checkResult = $conn->query($sql2);
                if ($checkResult->num_rows > 0){
                    $row = $checkResult->fetch_assoc();
                    if ($row['advisor'] == "notSet"){
                        $sql3 = "UPDATE androidlogin SET advisor = '$advisor' WHERE username = '$student'";
                        if ($conn->query($sql3) === true){
                            $response['message'] = "advisor set successfully";
                            $response['success'] = true;
                        }
                        else{
                            $response['message'] = $conn->error;
                            $response['success'] = false;
                        }
                    }
                    else{
                        $response['message'] = "hasAdvisor";
                        $response['success'] = false;
                    }
                }
                else{
                    $response['message'] = "student not found";
                    $response['success'] = false;
                }
            }
            else if ($reqtype == "reject"){
                $response['message'] = "rejected successfully";
                $response['success'] = true;
            }
        }
        else{
            $response['message'] = $conn->error;
            $response['success'] = false;
        }
    }
    else{
        $response['message'] = "post not set";
        $response['success'] = false;
    }
}catch (Exception $e){
    $response['message'] = $e->getMessage();
    $response['success'] = false;
}finally{
    echo json_encode($response);
    $conn->close();
}
