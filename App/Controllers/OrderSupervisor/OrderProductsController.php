<?php

namespace App\Controllers\OrderSupervisor;

use \Core\View;
use \App\Flash;
use \App\Models\Order_Products;


class OrderProductsController extends SupervisorAuthenticated
{
   /**
    *  showing  all the orders from orders_products table
    */
   public function indexAction()
   {
      $orders_products=Order_Products::getAllOrdersProducts();
      View::renderTemplate('Supervisor/orders_products/index.html',[
      'orders_products' => $orders_products ?? null,
      ]);
   }
}