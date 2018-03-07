<?php
$MailClass = include 'Mail.inc';
$conn = include 'Connection.php';
if ($conn->connect_error) {
    die("ERROR CONNECTION " . $conn->connect_error);
}


try {
    /* Handle exception if values are not set
    */

    if (isset($_POST['ESignUp']) && isset($_POST['PSignUp']) && isset($_POST['USignUp'])){
        $username = mysqli_real_escape_string($conn, $_POST['USignUp']);
        $pass = mysqli_real_escape_string($conn, $_POST['PSignUp']);
        $email = mysqli_real_escape_string($conn, $_POST['ESignUp']);
        $salt = generateRandomString();
        $pass = encrypt::encode($pass . $salt, $salt);
        /* Check that the username and email should be new and unique
        */
        if (isUserExisted($username, NULL, $conn) || isUserExisted(NULL, $email, $conn))
            echo "ایمیل یا نام کاربری تکراری است.";
        else {
            /* Add new user in table
            */
            $hash = md5(rand(0, 1000));
            $sql = "INSERT INTO students (user_name,password,email,salt,hash)
                            VALUES('$username','$pass','$email','$salt','$hash')";
            if ($conn->query($sql)) {
                echo true;
                $MailClass.Mail::firstVerification($email, $hash, $username);
            } else
                echo false;
        }
    }
} catch (Exception $e) {
    echo "ERROR";
}
$conn->close();

/* This method will create the salt string
* @param int $length
* @return string the result
*/
function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/* This method will check if the user exist
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
class encrypt
{

/* This method will encode the password
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