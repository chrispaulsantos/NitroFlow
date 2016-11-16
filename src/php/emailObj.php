<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 8/18/16
 * Time: 11:09 AM
 */

require "../includes/PHPMailer/class.phpmailer.php";
require "../includes/PHPMailer/class.smtp.php";

class email {
    private $mail;

    public function __construct(){
        $this->mail = new PHPMailer();
        $this->mail->SMTPDebug = false;
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'nitroflow.alerts';
        $this->mail->Password = 'Crableg12';
        $this->mail->SMTPSecure = 'tls';
        $this->mail->Port = 587;
        $this->mail->setFrom('mail@nitroflow.com','Admin');
        // Always BCC Teddy and I
        $this->mail->addBCC("chrissantosproduction@gmail.com");
        $this->mail->addBCC("theodorexd@gmail.com");
    }

    public function create($addresses,$sub,$body){
        foreach ($addresses as $address){
            $this->mail->addAddress($address);
        }

        $this->mail->Subject = $sub;
        $this->mail->Body    = $body;
    }
    public function send(){
        if(!$this->mail->send()) {
            //echo 'Message could not be sent.';
            //echo 'Mailer Error: ' . $this->mail->ErrorInfo;
        } else {
            //echo 'Message has been sent';
        }
    }
}