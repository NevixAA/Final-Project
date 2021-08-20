<?php

namespace App\Models;

use PDO;
use DateTime;
use \App\Token;

class Order_Products extends \Core\Model 
{
    // Class constructor
    public function __construct($orderID,$productsID,$colorID,$sizeID,$price,$quantity)
    {
        $this->order_id   = $orderID;
        $this->product_id = $productsID;
        $this->color_id = $colorID;
        $this->size_id = $sizeID;
        $this->price      = $price;
        $this->quantity   = $quantity;
    }


    // return assoc array of orders_products from database table
    public static function getAllProductsByOrderID($orderID)
    {
        $db = static::getDB();
        $stmt = $db->query('SELECT * FROM orders_products WHERE order_id = order_id');
        $stmt->bindValue(':order_id',$orderID,PDO::PARAM_INT);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Save order in database
    public function save()
    {
        $db = static::getDB();
        $stmt = $db->prepare("INSERT INTO orders_products (order_id, product_id, color_id, size_id, price, quantity) VALUES (:order_id, :product_id, :color_id, :size_id, :price, :quantity)");      
        $stmt->bindValue(':order_id', $this->order_id, PDO::PARAM_INT);
        $stmt->bindValue(':product_id', $this->product_id, PDO::PARAM_INT);
        $stmt->bindValue(':color_id', $this->color_id, PDO::PARAM_INT);
        $stmt->bindValue(':size_id', $this->size_id, PDO::PARAM_INT);
        $stmt->bindValue(':price', $this->price, PDO::PARAM_STR);
        $stmt->bindValue(':quantity', $this->quantity, PDO::PARAM_STR);
        return $stmt->execute();
    }

}