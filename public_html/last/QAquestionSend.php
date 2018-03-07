<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 5/28/2017
 * Time: 4:33 PM
 */
    $serverName = "localhost";
    $dbUsername = "id554796_leopard";
    $dbPassword = "m.emami1391";
    $dbName = "id554796_database1";
    $tableName = "QAquestions";

    $conn = new mysqli($serverName, $dbUsername, $dbPassword, $dbName);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    try{
        $QAquestions = "SELECT QuestionTxt, QuestionPic FROM $tableName";
        if(!($result = $conn->query($QAquestions)))
            die($conn->error);
        if($result->num_rows){
            $questions = [];
            while($row = $result->fetch_assoc()){
                $questions[] = $row;
            }
            echo json_encode(array($tableName => $questions));
        }
    } catch(Exception $e){
        echo "Error ", $e->getMessage();
    }