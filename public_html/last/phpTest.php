<?php
$servername = "localhost";
$usernameDB = "id554796_leopard";
$passwordDB = "m.emami1391";
$dbname = "id554796_database1";

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if(isset($_POST['USER']) && isset($_POST['PASS'])) {
    $username = $_POST['USER'];
    $pass = $_POST['PASS'];
    if (isset($_POST['TYPE']))
        $type = $_POST['TYPE'];
    else
        $type = "student";
    if(isUserExisted($username, NULL, $conn, $type))
    {
        $u = getUserByUserAndPassword($username, $pass, $conn, $type);
        if ($u != null) {
            $user['success'] = true;
            $user['username'] = $u['username'];
            $user['email'] = $u['email'];
            $user['name'] = $u['name'];
            $user['familyName'] = $u['familyName'];
            if ($type == "student") {
                $user['field'] = $u['Field'];
                $user['advisor'] = $u['advisor'];
            }else{
            }
            $user['message'] = "Connected";
        } else {
            $user['message'] = "Password Incorrect";
            $user['success'] = false;
        }
    }else {
        $user['message'] = "Not Exists";
        $user['success'] = false;
    }
}
else{
    $user['message'] = "Not Connected";
    $user['success'] = false;
}
echo json_encode($user);
$conn->close();
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
function getUserByUserAndPassword($user, $password, $conn, $type) {
    if ($type == "advisor")
        $tableName = "Advisors";
    else
        $tableName = "androidlogin";
    $stmt = $conn->prepare("SELECT * FROM $tableName WHERE username = ?");
    $stmt->bind_param("s", $user);
    if ($stmt->execute()) {
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $salt = $user['salt'];
        if (trim($password.$salt) == trim(Encrypt::decode($user['password'],$salt))) {
            return $user;
        }
    } else {

        return NULL;
    }
}
//class encrypt {
//    /********* Encode *********/
//    public static function encode($pure_string, $encryption_key) {
//        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
//        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
//        $encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, md5(base64_encode(trim($encryption_key))), utf8_encode(trim($pure_string)), MCRYPT_MODE_ECB, $iv);
//        return base64_encode($encrypted_string);
//    }
//
//    /********** Decode ************ */
//    public static function decode($encrypted_string, $encryption_key) {
//        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
//        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
//        $decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, md5(base64_encode(trim($encryption_key))),base64_decode(trim($encrypted_string)), MCRYPT_MODE_ECB, $iv);
//        return $decrypted_string;
//    }
//
//}