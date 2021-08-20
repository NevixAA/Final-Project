<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

/**
 * Home controller
 *
 */
class Home extends \Core\Controller
{

    // Show the index page
    public function indexAction()
    {
        View::renderTemplate('Home/index.html');
    }


}
