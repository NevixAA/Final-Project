<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Mailer;
use \App\Models\Other;

class Signup extends \Core\Controller
{

    // Show the signup page
    public function newAction()
    {
        View::renderTemplate('Signup/new.html');
    }


    // Sign up a new user
    public function createAction()
    {
        $user = new User($_POST); 
        $mailMessage = $this->getEmailMessage();

        if ($user->save()) {
            $mail = new Mailer();
            Mailer::send($_POST['email'],'Regisration Femin',$mailMessage,'<p>regisration complete</p>');  
            $this->redirect('/signup/success');
        } else {
            View::renderTemplate('Signup/new.html', [
                'user' => $user
            ]);
        }
    }


    // Show the signup success page
    public function successAction()
    {
        View::renderTemplate('Signup/success.html');
    }


    // get Email Message
    public function getEmailMessage()
    {
        $value = Other::getValueByID(4);
        return $value->string_value;
    }
}
