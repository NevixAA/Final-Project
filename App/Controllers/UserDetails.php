<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Models\Order;
use \App\Models\Color;
use \App\Models\Size;




class UserDetails extends Authenticated
{
    // User details
    public function indexAction()
    {
        View::renderTemplate('UserDetails/index.html',[
            'user'   => User::findByID($_SESSION['user_id'])
        ]);
    }

    // User orders information
    public function ordersHistoryAction()
    {
        View::renderTemplate('UserDetails/orders_history.html',[
            'orders' => $this->buildOrdersHistory($_SESSION['user_id'])
        ]);
    }


    // Attach ordered products to orders history
    public function buildOrdersHistory($userID)
    {
        $orders = [];
        $fetchedOrders = Order::getOrdersByUserID($userID);
        foreach ($fetchedOrders as $order) {
            $orderId = $order['order_id'];
            $ordered_products = Order::getProductsByOrderID($orderId); // Get ordered product for each order id
            $orderProds = [];
            foreach ($ordered_products as $key => $product) {
                $product['size_d_name'] = Size::getSizeDisplayNameByID($product['size_id'])['size_d_name'];
                $product['color_d_name'] = Color::getColorDisplayNameByID($product['color_id'])['color_d_name'];
                $orderProds[] = $product;
            }
            $order['ordered_products'] = $orderProds;
            $orders[$orderId] = $order;
        }
        return $orders;
    }




}
