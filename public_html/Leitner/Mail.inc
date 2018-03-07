<?php

/**
 * Created by PhpStorm.
 * User: TheMn
 * Date: 7/31/2017
 * Time: 10:13 AM
 */
class Mail
{
    public static function firstVerification($to, $hash, $username)
    {
        $subject = 'عضویت در یادمان';
        $message = '
سلام، وقت به خیر
    با تشکر از عضویت شما
حساب کاربری شما در نرم افزار یادمان ساخته شد، برای وارد شدن با نام کاربری
' . $username . '
لازم است ابتدا ایمیل خود را تأیید کنید
----------------------

    برای فعالسازی ایمیلتان، بر روی لینک زیر کلیک کنید:
    http://fekr-afarinan.ir/Leitner/verify.php?email=' . $to . '&hash=' . $hash . '
    
    این ایمیل از طرف تیم پشتیبانی نرم افزار یادمان ارسال شده است
    ';
        $header = 'From:yademan@fekr-afarinan.ir' . "\r\n";
        return mail($to, $subject, $message, $header);
    }

    public static function editVerification($to, $hash, $username)
    {
        $subject = 'تغییر ایمیل یادمان';
        $message = '
سلام، وقت به خیر
 ایمیل شما در نرم افزار یادمان با موفقیت تغییر کرد، برای تأیید ایمیل فعلی با نام کاربری
 ' . $username . '
لازم است ابتدا ایمیل خود را تأیید کنید
----------------------

    برای فعالسازی ایمیلتان، بر روی لینک زیر کلیک کنید:
    http://fekr-afarinan.ir/Leitner/verify.php?email=' . $to . '&hash=' . $hash . '
    
    این ایمیل از طرف تیم پشتیبانی نرم افزار یادمان ارسال شده است
    ';
        $header = 'From:yademan@fekr-afarinan.ir' . "\r\n";
        return mail($to, $subject, $message, $header);
    }

    public static function forgotPass($to, $username, $pass)
    {
        $subject = 'فراموشی رمز عبور یادمان'; // Give the email a subject
        $message = '
سلام، وقت به خیر
کاربر گرامی  ' . $username . '
 کلمه ی عبور شما در نرم افزار یادمان با این حساب کاربری
 ' . $pass . '
است، لطفاً وارد نرم افزار شده و از قسمت تنظیمات، این گذرواژه را تغییر دهید
----------------------
    
    این ایمیل از طرف تیم پشتیبانی نرم افزار یادمان ارسال شده است
    ';
        $header = 'From:yademan@fekr-afarinan.ir' . "\r\n";
        return mail($to, $subject, $message, $header);
    }
}