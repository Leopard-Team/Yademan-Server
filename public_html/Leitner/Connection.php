<?php
/**
 * The connection Class helps to create connection and avoids repeating
 * Created by PhpStorm.
 * User: TheMn
 * Date: 7/23/2017
 * Time: 8:43 AM
 */
$serverName = "localhost";
$dbUsername = "fekrafa1_yademan";
$dbPassword = "@leo95pard";
$dbName = "fekrafa1_yademan";
return new mysqli($serverName, $dbUsername, $dbPassword, $dbName);
