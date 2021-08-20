<?php

namespace App\Models;

use PDO;
use \App\Models\Category;

class Discount extends \Core\Model
{
    public $errors = [];

    public function __construct($data) 
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }


    // Add new active discount
    public function save()
    {
        $this->validate();
        if (empty($this->errors)) {
            $db = static::getDB();
                $stmt = $db->prepare('INSERT INTO discounts_active(discount_id, category_id, start_date, end_date, percent, from_item, all_store) VALUES (:dscntId,:catId,:start_date,:endDate,:percent,:fromItem,:allStore)');
                return $stmt->execute([
                    ':dscntId'   => $this->dscntId,
                    ':catId'     => $this->catId    ?? 1,
                    'start_date' => date('Y-m-d\TH:i'),
                    ':endDate'   => $this->end_date,
                    ':percent'   => $this->percent   ?? null,
                    ':fromItem'  => $this->from_item ?? null,
                    ':allStore'  => $this->all_store 
                ]);
        }
    }


    /**
     * Find discounts
     * If all store discount is available apply it
     * Otherwise check for categories discounts
     */
    public static function findDiscounts()
    {
        $all_store_dscnts = self::getAllStoreDiscounts();
        if ($all_store_dscnts) {
            return self::getDscntItems($all_store_dscnts['discount_id'],$all_store_dscnts['percent'],null);
        }else {
            foreach (self::getDiscountCats() as $catId => $dscnt) {
                $dscnt_info = self::getDscntItems($dscnt['discount_id'],$dscnt['percent'],$dscnt['from_item'],$catId);
                if ($dscnt_info != null) {
                    break;
                }
            }
        }
        // Returns discount information
        return $dscnt_info ?? [];

    }


    // Find a discount with cart items and return discount info
    public static function getDscntItems($discount_id,$percent,$from_item = null,$cat_id = null)
    {
        switch ($discount_id) { 
            case '1':           // One plus one
                return self::discountOnePlusOne(self::findDiscountProducts($cat_id));
            case '2':           // All store precentage discount
                return self::discountInPrecent(self::findDiscountProducts($cat_id),$percent,$cat_id);
            case '3':           // The nth product gets a precentage discount
                return self::discountProdPrecent(self::findDiscountProducts($cat_id),$from_item-1,$percent);
        }
        return [];
    }


    /**
     * Find discount categories and match it with
     * cart items categories
     * return array of each index is category id and inside discount_id,percent,from_item
     */
    public static function getDiscountCats()
    {
        $cat_ids = [];
        $all_dscnt_cats = self::getCatsDiscounts();
        if (isset($_SESSION['cart_items'])) {
            foreach ($_SESSION['cart_items'] as $item) {
                foreach ($all_dscnt_cats as $cat) {
                    if ($cat['category_id'] == $item['category_id'])
                        $cat_ids[$item['category_id']] = [
                            'discount_id'  => $cat['discount_id'], 
                            'percent'      => $cat['percent'],
                            'from_item'    => $cat['from_item']
                            ];             
                }
            }
        }
        return $cat_ids;
    }


    /**
     * Find products to calculate discounts
     * Not passing a parameter means find all store products
     * Otherwise passing category_id will look for products from specific category
     */
    public static function findDiscountProducts($categoryId = null)
    {
        $discount_prods = [];
        if (isset($_SESSION['cart_items'])) {
            foreach ($_SESSION['cart_items'] as $prod) {
                if ($categoryId == null || $prod['category_id'] == $categoryId) {
                    for ($i=0; $i < $prod['quantity']; $i++) { 
                        $discount_prods[] = $prod;
                    }
                }
            }
        }
        return $discount_prods;
    }


    /**
     * Find cheapest discount products
     */
    public static function cheapDiscountProd($products)
    {
        $discountProd = array_shift($products);
        foreach ($products as $prod) {    // Find the cheapest product
            $discountProd = $prod['product_price'] < $discountProd['product_price'] ? $prod : $discountProd;
        }
        return $discountProd;
    }


    /**
     * Discount - 1+1 buy one get the cheap from both - for free
     * If there is no discount zero will return
     */
    public static function discountOnePlusOne($products)
    {
        if (! empty($products) && count($products) > 1) {    
            $dscnt_prod = self::cheapDiscountProd($products);
            return [
                'string' => 'מבצע 1+1 על הפריט ' . $dscnt_prod['product_name'], 
                'value'  => $dscnt_prod['product_price']
            ];
        }
        return null;
    }


    /**
     * Discount - in precentage for the nth item 
     * Buyer must buy n products so the nth product will get the discount
     */
    public static function discountProdPrecent($products,$n,$precent)
    {
        if (! empty($products) && count($products) >= $n+1) {
            $dscnt_prod = self::cheapDiscountProd($products);
            return [
                'string' => $precent . '% ' . 'הנחה על פריט ' . ($n+1) . ' - ' . $dscnt_prod['product_name'],
                'value'  => $dscnt_prod['product_price'] * ($precent / 100)
            ];
        }
        return null;

    }


    /**
     * Discount - all category or store
     */
    public static function discountInPrecent($products,$precent,$catId = null)
    {
        $discountOn = 'כל החנות';
        $price_sum = 0;
        if ($catId != null) 
            $discountOn = 'הקטגוריה ' . Category::getCategoryByID($catId)->display_name;
        foreach ($products as  $prod) 
            $price_sum += $prod['product_price'];
        return [
            'string' => $precent . '% על ' . $discountOn, 
            'value' => $price_sum * ($precent / 100)
        ];
    }


    /**
     * Discount - check if user gets a free shipping
     * Return free shipping string
     */
    public static function checkForFreeShipping($sub_total)
    {
        $freeShipPrice = self::getPriceForFreeDelivery();
        if ($sub_total >= $freeShipPrice) {
            return 'משלוח חינם מעל הסכום ' . $freeShipPrice . ' ש"ח';
        }
        return false;
    }


    // Get price for free delivery
    public static function getPriceForFreeDelivery()
    {
        return Other::getValueByID(7)->string_value;;
    }











    /**
     * Validations
     */

    // Call for the right validation function by discount Id
    public function validate()
    {
        switch ($this->dscntId) {
            case '1':
                $this->vldDscntOnePlusOne();
                break;
            case '2':
                $this->vldDscntInPercent();
                break;
            case '3':
                $this->vldDscntProdPercent();
        }
    }


    // Validate one plus one discount
    public function vldDscntOnePlusOne()
    {
        $this->vldDate(); 
        $this->vldCatDscntExists();
    }


    // Validate store percentage discount
    public function vldDscntInPercent()
    {
        $this->vldDate();
        $this->vldPercent();
        $this->vldCatDscntExists();
        $this->vldStoreDscntExists();
    }


    // Validate the nth item percentage discount
    public function vldDscntProdPercent()
    {
        $this->vldDate();
        $this->vldPercent();
        $this->vldFromItem();
        $this->vldCatDscntExists();

    }


    // Add error message if store discount exists
    public function vldStoreDscntExists()
    {
        if (empty($this->catId) && $this->isStoreDiscountExists()) 
            $this->errors[] = 'לא ניתן להוסיף הנחה, קיימת הנחה כללית על החנות';
    }

    
    // Add error message if category discount exists
    public function vldCatDscntExists()
    {
        if (! empty($this->catId) && $this->isCatDiscountExists()) {
            $this->errors[] = 'קיימת כבר הנחה כזאת באתר!';
        }
    }


    // Validate from item 
    public function vldFromItem()
    {
        if (empty($this->from_item) || $this->from_item < 0 || $this->from_item > 5) 
            $this->errors[] = 'הנחת החל מפריט צריכה להיות בטווח 5 - 1';
    }


    // Validate percentage
    public function vldPercent()
    {
        if (empty($this->percent) || $this->percent < 0 || $this->percent > 100) 
            $this->errors[] = 'אחוזים צריכים להיות בטווח 100 - 1';
    }


    // Validate date
    public function vldDate()
    {
        if (empty($this->end_date)) 
            $this->errors[] = 'יש להכניס תאריך';
        else if ($this->end_date < date('Y-m-d\TH:i'))
            $this->errors[] = 'התאריך שהכנסת קטן מהתאריך הנוכחי';
    }


    // Check if category discount exists
    public function isCatDiscountExists()
    {
        return self::getDiscountByID($this->dscntId,$this->catId);
    }


    // Check if store discount exists
    public function isStoreDiscountExists()
    {
        return self::getAllStoreDiscounts();
    }















    /**
     * Active discounts
     * Queries
     */

    // Get all active discounts
    public static function getActiveDiscounts()
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM discounts_active JOIN discounts USING(discount_id) JOIN categories USING(category_id)');
        $stmt->execute(); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    /**
     * Get all category discounts
     * end_date should be bigger than today
     */
    public static function getCatsDiscounts()
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT category_id,discount_id,percent,from_item FROM discounts_active WHERE all_store = 0 AND end_date > NOW()');
        $stmt->execute(); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    /**
     * Get all store discounts without category discounts
     * * end_date should be bigger than today
     */
    public static function getAllStoreDiscounts()
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM discounts_active WHERE all_store = 1 AND end_date > NOW()');
        $stmt->execute(); 
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Get discounts 
    public static function getDiscounts()
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM discounts');
        $stmt->execute(); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    // Get discount by id
    public static function getDiscountByID($dscntId,$catId)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM discounts_active WHERE discount_id = :dscntId AND category_id = :catId');
        $stmt->execute([':dscntId'=>$dscntId,':catId'=>$catId]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Remove discount from active discounts
    public static function removeDiscount($dscntId,$catId)
    {
        $db = static::getDB();
        $stmt = $db->prepare('DELETE FROM discounts_active WHERE discount_id = :dscntId AND category_id = :catId');
        return $stmt->execute([':dscntId'=>$dscntId,':catId'=>$catId]); 
    }



}

?>