<?php
session_start();
$conn = include 'Connection.php';
$CSRF = include 'noscrf.php';
if ($conn->connect_error) {
    die("ERROR CONNECTION " . $conn->connect_error);
}
/**
 * Handle exception if user and pass are not set
 */

if (isset($_POST['USER']) && isset($_POST['PASS'])) {

    $username = mysqli_real_escape_string($conn, $_POST['USER']);
    $token= $CSRF . NoCSRF::generate($username);
    $pass = mysqli_real_escape_string($conn, $_POST['PASS']);
    /**
     * User can login only if its account is existed
     */
    if (isUserExisted($username, NULL, $conn)) {
        $u = getUserByUserAndPassword($username, $pass, $conn);
        /**
         * Accepts the username and password
         */
        if ($u != null) {

            $user['success'] = true;
            $user['user_name'] = $u['user_name'];
            $user['email'] = $u['email'];
            $user['isVerified'] = $u['isVerified'];
            if ($u['phoneNumber'] != '-1')
                $user['isVerified'] = 1;
            $user['budget'] = $u['budget'];
//            echo $token;
            $user['token'] = $token;
            $id = $u['id'];
        } else {
            $user['message'] = "رمز عبور صحیح نمیباشد";
            $user['success'] = false;
        }
    } else {
        $user['message'] = "کاربری با این نام کاربری وجود ندارد";
        $user['success'] = false;
    }
} else {
    $user['message'] = "خطا";
    $user['success'] = false;
}

/**
 * Sends the user as a json
 */
//echo json_encode($user);
if ($user['success']) {
    $sql = "SELECT * FROM solds WHERE student_id='$id'";
    $out = array();
    $res = $conn->query($sql);
    if ($res->num_rows > 0) {
        $i = 1;
        $user['lesson_id'] = array();
        while ($row = $res->fetch_assoc()) {
            $user['lesson_id'][$i] = (int)$row['lesson_id'];
            $i++;
        }
        $user['lesson_id'] = array_values($user['lesson_id']);
    } else
        $user['lesson_id'] = array();
}
echo json_encode($user);
$conn->close();

/**
 * This method will check if the user exist
 * @param null $username the username that should be checked
 * @param null $email the email that should be checked
 * @param $conn
 * @return bool existence of user
 */
function isUserExisted($username = NULL, $email = NULL, $conn)
{
    $tableName = "students";
    if ($username != NULL) {
        $stmt = $conn->prepare("SELECT user_name from $tableName WHERE user_name = ?");
        $stmt->bind_param("s", $username);
    }
    if ($email != NULL) {
        $stmt = $conn->prepare("SELECT user_name from $tableName WHERE email = ?");
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

/**
 * This method will get the user with unique specifications
 * @param $user
 * @param $password
 * @param $conn
 * @return null as there is not user
 */
function getUserByUserAndPassword($user, $password, $conn)
{
    $tableName = "students";
    $sql = "SELECT * FROM $tableName WHERE user_name='$user' ";
    $res = $conn->query($sql);
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $u['phoneNumber'] = $row['phoneNumber'];
        $u['user_name'] = $row['user_name'];
        $u['password'] = $row['password'];
        $u['salt'] = $row['salt'];
        $u['email'] = $row['email'];
        $u['isVerified'] = $row['isVerified'];
        $u['budget'] = $row['budget'];
        $u['id'] = $row['id'];
        $salt = $u['salt'];
        if (trim($password . $salt) == trim(encrypt::decode($u['password'], $salt))) {
            return $u;
        }
        return NULL;
    } else {
        return NULL;
    }
}

class encrypt
{
    /**
     * This method will encode the password
     * @param $pure_string
     * @param $encryption_key
     * @return string the result
     */
    public static function encode($pure_string, $encryption_key)
    {
        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, md5(base64_encode(trim($encryption_key))), utf8_encode(trim($pure_string)), MCRYPT_MODE_ECB, $iv);
        return base64_encode($encrypted_string);
    }

    /**
     * this method will decode the password
     * @param $encrypted_string
     * @param $encryption_key
     * @return string the result
     */
    public static function decode($encrypted_string, $encryption_key)
    {
        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, md5(base64_encode(trim($encryption_key))), base64_decode(trim($encrypted_string)), MCRYPT_MODE_ECB, $iv);
        return $decrypted_string;
    }

}