<?php

/**
 * Created by PhpStorm.
 * User: asus
 * Date: 9/19/2017
 * Time: 1:58 PM
 */
class SMS
{
    static $APIKey = "331956a09ca5bfe048bb56a0";
    static $SecretKey = ")l!e(o9@5*p#a&r%d^";
    static $LineNumber = "10009035";

    protected function getAPIMessageSendUrl()
    {
        return "http://RestfulSms.com/api/MessageSend";
    }

    protected function getApiTokenUrl()
    {
        return "http://RestfulSms.com/api/Token";
    }

    public function __construct($APIKey, $SecretKey, $LineNumber)
    {
        $this->APIKey = $APIKey;
        $this->SecretKey = $SecretKey;
        $this->LineNumber = $LineNumber;
    }

    public function SendMessage($MobileNumbers, $Messages, $SendDateTime = '')
    {

        $token = $this->GetToken($this->APIKey, $this->SecretKey);
        if ($token != false) {
            $postData = array(
                'Messages' => $Messages,
                'MobileNumbers' => $MobileNumbers,
                'LineNumber' => $this->LineNumber,
                'SendDateTime' => $SendDateTime,
                'CanContinueInCaseOfError' => 'false'
            );

            $url = $this->getAPIMessageSendUrl();
            $SendMessage = $this->execute($postData, $url, $token);
            $object = json_decode($SendMessage);
            if (is_object($object)) {
                $array = get_object_vars($object);
                if (is_array($array)) {
                    $result = $array['Message'];
                } else {
                    $result = false;
                }
            } else {
                $result = false;
            }

        } else {
            $result = false;
        }
        return $result;
    }

    private function GetToken()
    {
        $postData = array(
            'UserApiKey' => $this->APIKey,
            'SecretKey' => $this->SecretKey,
            'System' => 'php_rest_v_1_1'
        );
        $postString = json_encode($postData);

        $ch = curl_init($this->getApiTokenUrl());
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, count($postString));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);

        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result);

        if (is_object($response)) {
            $resultVars = get_object_vars($response);
            if (is_array($resultVars)) {
                @$IsSuccessful = $resultVars['IsSuccessful'];
                if ($IsSuccessful == true) {
                    @$TokenKey = $resultVars['TokenKey'];
                    $resp = $TokenKey;
                } else {
                    $resp = false;
                }
            }
        }

        return $resp;
    }

    private function execute($postData, $url, $token)
    {

        $postString = json_encode($postData);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'x-sms-ir-secure-token: ' . $token
        ));
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, count($postString));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }


    public static function first_verification($to, $activation_code)
    {

        try {

            date_default_timezone_set("Asia/Tehran");

            // your sms.ir panel configuration
//            $APIKey = "enter your api key ...";
//            $SecretKey = "enter your secret key ...";
//            $LineNumber = "enter your line number ...";

            // your mobile numbers
            $MobileNumbers = array('091xxxxxxxx', '092xxxxxxxx', '093xxxxxxxx');

            // your text messages
            $Messages = array('text1', 'text2', 'text3');

            // sending date
            @$SendDateTime = date("Y-m-d") . "T" . date("H:i:s");

            $SmsIR_SendMessage = new SMS(static::$APIKey, static::$SecretKey, static::$LineNumber);
            $SendMessage = $SmsIR_SendMessage->SendMessage($MobileNumbers, $Messages, $SendDateTime);

        } catch
        (Exeption $e) {
            echo 'Error SendMessage : ' . $e->getMessage();
        }
    }

    public static function forgot_password()
    {

    }
}