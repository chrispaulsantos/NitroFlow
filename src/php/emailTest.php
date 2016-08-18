<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 8/18/16
 * Time: 11:09 AM
 */

require "../includes/class.phpmailer.php";
require "../includes/class.smtp.php";

$mail = new PHPMailer();

$mail->isSMTP();
$mail->Host = 'smtp.google.com';
$mail->Username = 'chrissantosproduction';
$mail->Password = 'Jedaii2017';
$mail->SMTPSecure = 'ssl';
$mail->Port = 465;

$mail->setFrom('mail@nitroflow.com','Mailer');
$mail->addAddress('chrissantosproduction@gmail.com','Chris');

$mail->Subject = 'Here is the subject';
$mail->Body    = 'This is a test email';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}