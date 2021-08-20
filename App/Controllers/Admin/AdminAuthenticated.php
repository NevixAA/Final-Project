<?php

namespace App\Controllers\Admin;


abstract class AdminAuthenticated extends \Core\Controller
{

    // Check if admin is logged in
    protected function before()
    {
        $this->requireAdminLogin();
    }
}
