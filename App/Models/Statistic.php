<?php

namespace App\Models;

use PDO;

class Statistic extends \Core\Model
{

    /**
     * Get users which registered and active between years $startYear - $endYear
     * Active user - user who created an order
     */
    public static function getActiveUsersCount($startYear,$endYear)
    {
        $db = static::getDB();
        $startDate = $startYear . '-01-01';
        $endDate   = $endYear . '-01-01';
        $stmt = $db->prepare('SELECT COUNT(DISTINCT users.id) active_users FROM users JOIN orders WHERE users.id = orders.user_id AND orders.start_date >= :startDate AND orders.start_date < :endDate AND users.created_at >= :startDate AND users.created_at < :endDate');
        $stmt->execute([':startDate'=>$startDate, ':endDate'=>$endDate]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Get totals users which registered between years $startYear - $endYear
    public static function getTotalUsersCount($startYear,$endYear)
    {
        $db = static::getDB();
        $startDate = $startYear . '-01-01';
        $endDate   = $endYear . '-01-01';
        $stmt = $db->prepare('SELECT COUNT(*) as total_users FROM users WHERE users.created_at >= :startDate AND users.created_at < :endDate');
        $stmt->execute([':startDate'=>$startDate, ':endDate'=>$endDate]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Get new users that registered between startDate - endDate
    public static function getNewUsersByDate($startDate,$endDate)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT COUNT(*) AS new_users FROM users WHERE users.created_at >= :startDate AND users.created_at <= :endDate');
        $stmt->execute([':startDate'=>$startDate, ':endDate'=>$endDate]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    // Get new orders that created between startDate - endDate
    public static function getNewOrdersByDate($startDate,$endDate)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT COUNT(*) AS new_orders FROM orders WHERE orders.start_date >= :startDate AND orders.start_date <= :endDate');
        $stmt->execute([':startDate'=>$startDate, ':endDate'=>$endDate]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    // Get sold products that sold between startDate - endDate
    public static function getSoldProductsByDate($startDate,$endDate)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT COUNT(*) as sold_products FROM orders JOIN orders_products WHERE orders.order_id = orders_products.order_id AND orders.start_date >= :startDate AND orders.start_date <= :endDate');
        $stmt->execute([':startDate'=>$startDate, ':endDate'=>$endDate]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    // Get details about categories
    public static function getCategoriesStatistics()
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT categories.display_name,SUM(orders_products.quantity) AS sold_products FROM products, orders_products, categories WHERE products.product_id = orders_products.product_id AND categories.category_id = products.category_id GROUP BY products.category_id');
        $stmt->execute(); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>