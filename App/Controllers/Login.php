<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;


class Login extends \Core\Controller
{

    // Show the login page
    public function newAction()
    {
        if (!Auth::getUser())
            View::renderTemplate('Login/new.html');
        else 
            $this->redirect('/');
    }

    // Log in a user
    public function createAction()
    {
        $user = User::authenticate($_POST['email'], $_POST['password']);
        
        $remember_me = isset($_POST['remember_me']);

        if ($user) {

            Auth::login($user, $remember_me);
            Flash::addMessage('התחברת בהצלחה לאתר!', Flash::SUCCESS);

            $this->redirectToHomepage($user);
        } else {
            Flash::addMessage('ההתחברות נכשלה, נסה שוב', Flash::WARNING);

            View::renderTemplate('Login/new.html', [
                'email' => $_POST['email'],
                'remember_me' => $remember_me
            ]);
        }
    }


    /**
     * Redirect login user according it's type
     * Registered user - to homepage '/'
     * Admin - to '/admin/index'
     * Order Supervisor - to '/OrderSupervisor/index'
     */
    public function redirectToHomepage($user)
    {
        if ($user->type == '1') {
            $this->redirect('/admin');
        }else if ($user->type == '2') {
            $this->redirect(Auth::getReturnToPage());
        }else if ($user->type == '3') {
            $this->redirect('/supervisor');
        }
    }



    // Log out a user
    public function destroyAction()
    {
        Auth::logout();

        $this->redirect('/login/show-logout-message');
    }

    /**
     * Show a "logged out" flash message and redirect to the homepage. Necessary to use the flash messages
     * as they use the session and at the end of the logout method (destroyAction) the session is destroyed
     * so a new action needs to be called in order to use the session.
     *
     * @return void
     */
    public function showLogoutMessageAction()
    {
        Flash::addMessage('התנתקת בהצלחה מהאתר!');
        $this->redirect('/');
    }
}
