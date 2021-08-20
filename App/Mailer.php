<?php


namespace App;

use \App\ConfigMailer;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Mailer 
 */
class Mailer
{
    /**
     * Server settings
     */

    public static function send($to, $subject, $text, $html)
    {
        $mail=new PHPMailer(true);
        try {
            $mail->isSMTP();                                     // Send using SMTP
            $mail->Host       = ConfigMailer::Host;              // Set the SMTP server to send through
            $mail->SMTPAuth   = ConfigMailer::SMTPAuth;          // Enable SMTP authentication
            $mail->Username   = ConfigMailer::Username;          // SMTP username
            $mail->Password   = ConfigMailer::Password;          // SMTP password
            $mail->SMTPSecure = ConfigMailer::SMTPSecure;        // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = ConfigMailer::Port;              // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
            $mail->setFrom(ConfigMailer::Username, 'Femin online shop');
            $mail->addAddress($to, 'Joe User');     // Add a recipient.
            $mail->isHTML(true);                                       // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $text;
            $mail->html = $html;
            $mail->send();  
            echo "email sent"  ;                  // Set email format to HTML
        }catch(Exception $e) {
            echo $mail->ErrorInfo;
        }

    }
}
