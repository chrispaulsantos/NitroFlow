<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 8/18/16
 * Time: 11:09 AM
 */

require "../includes/PHPMailer/PHPMailerAutoload.php";

$mail = new PHPMailer();

$mail->SMTPDebug = 3;

$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'nitroflow.alerts';
$mail->Password = 'Crableg12';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$mail->setFrom('mail@nitroflow.com','Admin');
$mail->addAddress('chrissantosproduction@gmail.com','Chris');

$mail->Subject = 'Here is the subject';
$mail->Body    = 'This is a test email';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}