<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Flash;
use \App\Auth;
use \App\Models\Category;



class HomeController extends AdminAuthenticated
{
    // Load admin panel homepage
    public function indexAction()
    {
        View::renderTemplate('Admin/Home/index.html');
    }

}
