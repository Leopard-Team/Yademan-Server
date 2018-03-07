<?php
session_start();
/**
 * Delete studentCards from table
 * Created by PhpStorm.
 * User: mahdi
 * Date: 7/24/2017
 * Time: 3:18 PM
 */
$conn = include "Connection.php";
$csrf=include 'noscrf.php';
/**
 * Handle exception if values are not set
 */
    try {
        $user = $_POST['user'];
        $csrf . NoCSRF::check($user, $_POST);
        $size = $_POST['size'];
        $decode = json_decode($_POST['studentCards']);
        $input = json_decode($_POST['studentCards'], true);
        for ($i = 0; $i < $size; $i++) {
            /**
             * Delete studentCard with unique id
             */
            $id = $input[$i];
            $sql = "DELETE FROM studentCards WHERE id ='$id;'";
            if ($conn->query($sql)) {
                echo "success";
            } else
                echo $conn->error;
        }
    }catch (Exception $e){
        echo $e->getMessage();
    }



