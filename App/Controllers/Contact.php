<?php

namespace App\Controllers;

use \App\Mailer;
use \App\ConfigMailer;

use \Core\View;



class Contact extends \Core\Controller
{

    // Contact home page
    public function indexAction()
    {
        View::renderTemplate('Contact/index.html');
    }


    // After sending mail render success page
    public function emailSentSuccess()
    {
        View::renderTemplate('Contact/success.html');
    }

    /**
     * AJAX CALL - Send email 
     * $_POST['email'] - assoc array contains email's subject and body
     */
    public function sendEmail()
    {
        header('Content-Type: application/json');
        if (isset($_POST['email'])) {
            $email = json_decode($_POST['email'],true);
            Mailer::send('feminproject@gmail.com',$email['subject'],$email['body'],'<p'.$email['subject'].$email['body'].'</p>');
            echo "true";
        }
    }

}
