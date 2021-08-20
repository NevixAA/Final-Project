<?php

namespace App\Models;

use PDO;
use DateTime;
use \App\Token;
use \App\Models\Other;

class Order extends \Core\Model 
{
    // Class constructor
    public function __construct($order_id,$user_id,$today,$expDate,$totalprice)
    {
        $this->order_id    = $order_id;
        $this->user_id     = $user_id;
        $this->today       = $today;
        $this->expDate     = $expDate;
        $this->totalprice  = $totalprice;
    }


    // Return assoc array of orders from db table
    public static function getAllOrders()
    {
        $db = static::getDB();
        $stmt = $db->query('SELECT * FROM orders');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Get order details by order id
    public static function getOrderByID($id)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM orders WHERE order_id = :order_id');
        $stmt->execute([':order_id'=>$id]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Get orders by user's ID
    public static function getOrdersByUserID($id)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM orders WHERE user_id = :user_id');
        $stmt->execute([':user_id'=>$id]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Get all ordered products 
    public static function getAllOrderedProducts()
    {
        $db = static::getDB();
        $stmt = $db->query('SELECT * FROM orders JOIN orders_products USING(order_id)');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Generate new orderID
    public static function generateOrderID()
    {
        $db= static::getDB();
        $stmt = $db->prepare("SELECT COUNT(*) as newOrderID FROM orders");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['newOrderID'] +1;
        
    }

    
    // get order by products id to check if we can delete him
    public static function getOrderByProductID($productID)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM orders_products JOIN orders USING(order_id) WHERE orders_products.product_id = :product_id');
        $stmt->execute([':product_id'=>$productID]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Get 5 urgent(exp_date) orders
    public static function getUrgentOrders()
    {
        $condition=Other::getValueByID(1)->string_value;
        $Today=date('y:m:d');
        $NewDate=Date('y:m:d', strtotime('+'.$condition.'days'));
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM `orders` WHERE status=1 AND exp_date BETWEEN :exp_datea AND :exp_dateb');
        $stmt->bindValue(':exp_datea',$Today, PDO::PARAM_STR);
        $stmt->bindValue(':exp_dateb',$NewDate, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    // Change status of order if approved
    public static function approveOrder($id)
    {
        $db= static::getDB();
        $stmt = $db->prepare("UPDATE orders SET  status=1 WHERE order_id=:order_id");
        $stmt->bindValue(':order_id',$id, PDO::PARAM_INT);
        $stmt->execute();
    }


    // Get the orders from the last 7 days
    public static function getWeeklyAmountOFOrders()
    {
        $Today=date('y:m:d');
        $NewDate=Date('y:m:d', strtotime('-'.'7'.'days'));
        $db= static::getDB();
        $stmt = $db->prepare("SELECT COUNT(*) as weekly FROM `orders` WHERE start_date BETWEEN :start_dateb AND :start_datea");
        $stmt->bindValue(':start_datea',$Today, PDO::PARAM_STR);
        $stmt->bindValue(':start_dateb',$NewDate, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['weekly'];
    }


    // Get number of orders
    public static function getAmountOfOrders()
    {
        $db= static::getDB();
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM orders");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }


    // Get maximun order price
    public static function getMaxPrice()
    {
        $db= static::getDB();
        $stmt = $db->prepare("SELECT MAX(price) as max_price FROM orders");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['max_price'];
    }


    // remove order after send her
    public static function removeSentOrder($order_id)
    {
        $db= static::getDB();
        $stmt = $db->prepare("DELETE o, op FROM orders o, orders_products op WHERE o.order_id = :order_id AND o.order_id = op.order_id");
        return $stmt->execute([':order_id'=>$order_id]);
    }

    
    /**
     * Check if order is sent
     * order sent if order status is 1, 0 - order not sent.
     */
    public static function isOrderSent($productID)
    {
        $order = self::getOrderByProductID($productID);
        if ($order && $order[0]['status'] == '0') {
            return false;
        }
        return true;
    }


    // Save order in db
    public function save()
    {
        $db = static::getDB();
        $stmt = $db->prepare("INSERT INTO orders (order_id,user_id, start_date, exp_date, price, status) VALUES (:order_id, :user_id, :start_date, :exp_date, :price, :status)");      
        $stmt->bindValue(':order_id', $this->order_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindValue(':start_date', $this->today, PDO::PARAM_STR);
        $stmt->bindValue(':exp_date', $this->expDate, PDO::PARAM_STR);
        $stmt->bindValue(':price', $this->totalprice, PDO::PARAM_STR);
        $stmt->bindValue(':status', 0, PDO::PARAM_INT);
        return $stmt->execute();
    }


    // Get ordered products by order id
    public static function getProductsByOrderID($orderID)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM orders_products JOIN products USING(product_id) WHERE orders_products.order_id = :order_id');
        $stmt->execute(['order_id' => $orderID]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>
