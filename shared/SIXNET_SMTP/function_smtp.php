<?php
 require_once 'class.phpmailer.php';
 require_once 'class.phpmailer.smtp.php';
function _mail($to='', $subject = '' , $message = '', $additional_headers = '', $additional_parameters = '') //$html = true
{
    global $fromAddress;
    global $fromName;
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPDebug = 0;
    $mail->SMTPAuth = true;
    $mail->Host = SMTP_HOST;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->CharSet = 'UTF-8';

    $mail->SetFrom($fromAddress, $fromName);
    $mail->AddReplyTo($fromAddress, $fromName);
    $mail->Subject = $subject;
    if($message != strip_tags($message))
        $mail->isHTML(true);
    $mail->Body = $message;
//    $mail->addCustomHeader($additional_headers);  // napriklad: $mail->addCustomHeader("X-Priority: 3");
    //$mail->AltBody = $message;
    $mail->AddAddress($to, $fromName);
    if(!$mail->Send())
        return false;
    else
        return true;
}
//////////////////////////////////////////////////////////////// IN config.inc.php
/*
/////////////////////////////// SMTP /////////////////////////////////////////////
$fromAddress = '';
$fromName = '';

define('SMTP_HOST', 'smtp.sixnet.sk');
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');

include 'SIXNET_SMTP/function_smtp.php';
/////////////////////////////// SMTP END//////////////////////////////////////////
*/
///////////////////////////////////////////////////////////////
?>
