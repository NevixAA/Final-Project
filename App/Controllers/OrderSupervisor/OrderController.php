<?php

namespace App\Controllers\OrderSupervisor;

use \Core\View;
use \App\Flash;
use \App\Models\Order;



class OrderController extends SupervisorAuthenticated
{
   /**
    *  change the status of order to 1 => order has been approved by supervisor
    */
   public function approveAction()
   {
         Order::approveOrder($this->route_params['id']);
         $orders=Order::getAllOrders();
         $orders_products=Order::getAllOrderedProducts();
         $this->redirect('/supervisor');
   }


}