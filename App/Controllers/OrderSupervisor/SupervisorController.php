<?php

namespace App\Controllers\OrderSupervisor;

use \Core\View;
use \App\Flash;
use \App\Models\Order;
use \App\Models\User;
use \App\Models\Order_Products;
use \App\Models\Category;
use \App\Models\Supplier;
use \App\Mailer;

class SupervisorController extends SupervisorAuthenticated
{
    // Render supervisor homepage
    public function indexAction()
    {
        $UrgentOrders = Order::getUrgentOrders();
        $AmountOfOrders = Order::getAmountOfOrders(); // total number of orders
        $weeklyAmount = Order::getWeeklyAmountOFOrders(); // number of orders from last 7 days
        $maxPrice = Order::getMaxPrice();// highest order price
        $orders = Order::getAllOrders();
        $orders_products = Order::getAllOrderedProducts();
    
        View::renderTemplate('Supervisor/index.html',[
            'UrgentOrders' => $UrgentOrders ?? null,
            'AmountOfOrders' => $AmountOfOrders ?? null,
            'weekly' =>  $monthlyAmount ?? 0,
            'max' => $maxPrice ?? null,
            'orders' => $orders ?? null,
            'orders_products' => $orders_products ?? null,
        ]);
    }

    // Direct to supplier order page
    public function orderFromSupplierAction()
    {
        $UrgentOrders = Order::getUrgentOrders();
        $AmountOfOrders = Order::getAmountOfOrders(); // total number of orders
        $weeklyAmount = Order::getWeeklyAmountOFOrders(); // number of orders from last 7 days
        $maxPrice = Order::getMaxPrice();// highest order price

        $ProductsToOrder = Category::getLowQnttyProducts();// get every products that his quantity below 6
        View::renderTemplate('Supervisor/orderFromSupplier/index.html',[
            'UrgentOrders' => $UrgentOrders ?? null,
            'AmountOfOrders' => $AmountOfOrders ?? null,
            'weekly' =>  $monthlyAmount ?? 0,
            'max' => $maxPrice ?? null,
            'productsToOrder' => $ProductsToOrder ?? null,
        ]);
    }

    // Send order to deliver
    public function sendAction()
    {
        if (isset($_POST['send_order']))
        {
            $order = Order::getOrderByID($this->route_params['id']);
            $email = User::findEmailByID($order[0]['user_id']);
            if ($order) {
                Order::removeSentOrder($order[0]['order_id']);
                Mailer::send($email->email,'order sent','regrats, your order has been sent','<p>regrats, your order has been sent</p>');
            }
            $this->redirect('/supervisor');
        }
    }

    // Send email to supplier 
    public function sendToSupplierAction()
    {
        if(isset($_POST['send_to_supplier'])) 
        {
            $productID   = $_POST['send_to_supplier'];
            $product     = Category::getProductNameByID($productID);
            $productName = $product[0]['product_name'];
            $supplier    = Supplier::getSupplierByID($this->route_params['id']);
            $email       = $supplier[0]['supplier_email'];
        
            if($supplier) {
                Mailer::send($email,'out_of_stock','Dear supplier,your product:'.$productName.'quantity out of stock','<p>Dear supplier,your product quantity out of stock</p>');
                Flash::addMessage('מייל נשלח בהצלחה', Flash::SUCCESS);
                $this->redirect('/supervisor/orderFromSupplier');
            }
        }
        Flash::addMessage('שליחת המייל איננה צלחה', Flash::WARNING);
        $this->redirect('/supervisor');
    }

}
