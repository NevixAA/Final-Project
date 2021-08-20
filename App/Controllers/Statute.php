<?php

namespace App\Controllers;

use \Core\View;



class Statute extends \Core\Controller
{

    // Contact home page
    public function indexAction()
    {
        View::renderTemplate('Statute/index.html');
    }
}