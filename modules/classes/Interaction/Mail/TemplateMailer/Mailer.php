<?php

namespace Interaction\Mail\TemplateMailer;


use Interaction\Mail\PHPMailer\Exception;
use Interaction\Mail\PHPMailer\PHPMailer;

class Mailer {

    private $mailer = null;
    private $host = 'polytable.ru';
    private $from = 'no-reply@polytable.ru';
    private $reply = 'support@polytable.ru';
    private $charset = 'UTF-8';
    private $headers = [
        'X-Priority: 1 (Highest)',
        'X-MSMail-Priority: High',
        'Importance: High'
    ];
    //SMTP Settings
    private $smtp_username = 'no-reply@polytable.ru';
    private $smtp_password = 'yDKQsKZEiW0';
    private $smtp_protocol = 'ssl';
    private $smtp_port = 465;

    public function __construct() {
        //Mailer setup
        $this->mailer = new PHPMailer(true);
        $this->mailer->CharSet = $this->charset;
        $this->mailer->isSMTP(); // Setting up mailer to use SMTP
        $this->mailer->Host = $this->host;
        $this->mailer->SMTPAuth = true; // Use SMTP Authorisation
        $this->mailer->Username = $this->smtp_username;
        $this->mailer->Password = $this->smtp_password;
        $this->mailer->SMTPSecure = $this->smtp_protocol;
        $this->mailer->Port = $this->smtp_port;
        //Mail setup
        $this->mailer->setFrom($this->from);
        $this->mailer->addReplyTo($this->reply);
        foreach ($this->headers as $header)
            $this->mailer->addCustomHeader($header);
        $this->mailer->isHTML(true);

    }

    function send(Mail $mail){
        $this->mailer->addAddress($mail->to);
        $this->mailer->Subject = $mail->subject;
        $this->mailer->Body = $mail->template;
        try {
            //$this->mailer->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}