<?php
/*$servername = "localhost";
$usernameDB = "id554796_mohammad";
$passwordDB = "m.emami1391";
$dbname = "id554796_androiddb";*/
/*$servername = "localhost";
$usernameDB = "root";
$passwordDB = "m.emami1391";
$dbname = "androiddb";*/
$servername = "localhost";
$usernameDB = "id554796_leopard";
$passwordDB = "m.emami1391";
$dbname = "id554796_database1";
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
try {
    if (isset($_POST['ESignUp']) && isset($_POST['PSignUp']) && isset($_POST['USignUp'])&& isset($_POST['TSignUp'])){
        $username = $_POST['USignUp'];
        $pass = $_POST['PSignUp'];
        $email = $_POST['ESignUp'];
        $type = $_POST['TSignUp'];
        $salt = generateRandomString();
        $pass = Encrypt::encode($pass . $salt, $salt);
        if(isUserExisted($username, NULL, $conn, $type) || isUserExisted(NULL, $email, $conn, $type))
            echo "Duplicate";
        else {
            if ($type == "student") {
                $sql = "INSERT INTO androidlogin (username,password,email,salt) VALUES ('$username','$pass','$email','$salt')";
                if ($conn->query($sql))
                    echo "Success";
                else
                    echo "Failed";
            }
            if ($type == "advisor"){
                $sql = "INSERT INTO Advisors (username,password,email,salt) VALUES ('$username','$pass','$email','$salt')";
                if ($conn->query($sql))
                    echo "Success";
                else
                    echo $conn->error;
            }
        }
    } else {
        echo "NOT SET";
    }
}catch (Exception $e){
    echo "ERROE";
}

$conn->close();

//class encrypt {
//    public static function encode($pure_string, $encryption_key) {
//        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
//        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
//        $encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, md5(base64_encode(trim($encryption_key))), utf8_encode(trim($pure_string)), MCRYPT_MODE_ECB, $iv);
//        return base64_encode($encrypted_string);
//    }
//    public static function decode($encrypted_string, $encryption_key) {
//        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
//        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
//        $decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, md5(base64_encode(trim($encryption_key))),base64_decode(trim($encrypted_string)), MCRYPT_MODE_ECB, $iv);
//        return $decrypted_string;
//    }
//}
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function isUserExisted($username = NULL, $email = NULL , $conn, $type)
{
    if ($type == "advisor")
        $tableName = "Advisors";
    else
        $tableName = "androidlogin";
    if($username != NULL) {
        $stmt = $conn->prepare("SELECT username from $tableName WHERE username = ?");
        $stmt->bind_param("s", $username);
    }
    if($email != NULL){
        $stmt = $conn->prepare("SELECT username from $tableName WHERE email = ?");
        $stmt->bind_param("s", $email);
    }
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        return true;
    } else {
        $stmt->close();
        return false;
    }
}