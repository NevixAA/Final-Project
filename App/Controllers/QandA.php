<?php

namespace App\Controllers;

use \Core\View;



class QandA extends \Core\Controller
{

    // Contact home page
    public function indexAction()
    {
        View::renderTemplate('QandA/index.html');
    }
}