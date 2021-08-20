<?php

namespace App\Controllers\OrderSupervisor;

abstract class SupervisorAuthenticated extends \Core\Controller
{
    // Check if supervisor is logged in
    protected function before()
    {
        $this->requireSupervisorLogin();
    }
}
