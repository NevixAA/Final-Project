<?php

namespace App\Models;

use PDO;
use \App\Models\Other;



/**
 * Category model
 *
 */
class Category extends \Core\Model
{
    // Get all categories 
    public static function getAllCategories()
    {
        $db = static::getDB();
        $stmt = $db->query('SELECT * FROM categories');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Get All products
    public static function getAllProducts()
    {
        $db = static::getDB();
        $stmt = $db->query('SELECT *,(SELECT SUM(quantity) FROM products_colors WHERE product_id = products.product_id) as product_quantity FROM products,categories WHERE products.category_id = categories.category_id ORDER BY product_id ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Get specific category by category id
    public static function getCategoryByID($id)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM categories WHERE category_id = :id');
        $stmt->execute([':id'=>$id]); 
        return $stmt->fetch(PDO::FETCH_OBJ);
    }


    // Get single category by name
    public static function getCategoryByName($name)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM categories WHERE category_name = :name');
        $stmt->execute([':name'=>$name]); 
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    
    // Get all products using category ID
    public static function getProductByCategoryID($id)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT *,(SELECT SUM(quantity) FROM products_colors WHERE product_id = products.product_id) as product_quantity FROM products JOIN categories USING (category_id) WHERE products.category_id = :id');
        $stmt->execute([':id'=>$id]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    // Get product using categoryId and productId
    public static function getProduct($cat_id,$product_id)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM products WHERE category_id = :category_id AND product_id = :product_id');
        
        $stmt->execute([':category_id'=>$cat_id,':product_id'=>$product_id]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Get related products to same category products
    public static function getRelatedProducts($cat_id,$product_id)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM products JOIN categories USING (category_id) WHERE category_id = :cat_id AND NOT product_id = :product_id ORDER BY RAND() LIMIT 4');
        $stmt->execute([':cat_id'=>$cat_id,':product_id'=>$product_id]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Get product by product id
    public static function getProductByID($id)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT *,(SELECT SUM(quantity) FROM products_colors WHERE product_id = :product_id) 
        AS product_quantity
        FROM products 
        JOIN categories 
        USING(category_id) 
        WHERE product_id = :product_id');
        $stmt->execute([':product_id'=>$id]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete product by product id
    public static function deleteProduct($id)
    {
        $db = static::getDB();
        $stmt = $db->prepare('DELETE products,products_colors,products_sizes 
        FROM products 
        INNER JOIN products_colors,products_sizes,products_images 
        WHERE products_images.product_id = :id 
        AND products_colors.product_id = :id
        AND products_sizes.product_id = :id
        AND products.product_id = :id');
        return $stmt->execute([':id'=>$id]); 
    }

    // Get product name by id
    public static function getProductNameByID($id)
    {
        $db = static::getDB();
        $stmt = $db->prepare("SELECT (product_name) FROM products WHERE product_id=:product_id");
        $stmt->execute([':product_id'=>$id]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    /**
     * Search queries
     */
    public static function getProductsByName($name)
    {
        $db = static::getDB();
        $stmt = $db->prepare("SELECT *,(SELECT SUM(quantity) FROM products_colors JOIN products USING(product_id) WHERE product_name LIKE '%:p_name%') as product_quantity FROM products JOIN categories USING (category_id) WHERE product_name LIKE '%:name%'
        ");
        
        $stmt->execute([':p_name'=>$name, ':name'=>$name]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Get product sizes quantity
    public static function getProductsSizesQuantity()
    {
        $db = static::getDB();
        $stmt = $db->prepare("SELECT * FROM products_sizes ORDER BY product_id ASC");
        $stmt->execute(); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Get all colors
    public static function getColors()
    {
        $db = static::getDB();
        $stmt = $db->prepare("SELECT * FROM colors");
        $stmt->execute(); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   


    // Get product quantity by product ID
    public static function getProductQuantity($productID)
    {
        $db = static::getDB();
        $stmt = $db->prepare("SELECT sum(quantity) FROM products_colors WHERE product_id=:productID");
        
        $stmt->execute([':productID'=>$productID]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

     /**
     * Set quantity to product size
     * e.g size S - qnty 10
     * e.g size M - qnty 15 etc..
     */
    public static function setProductSizeQuantity($productID,$sideID,$colorID,$qnty)
    {
        //INSERT INTO products_colors(product_id,color_id,quantity) VALUES (1,1,(SELECT sum(quantity) FROM products_sizes WHERE product_id=1 AND color_id=1))
        $db = static::getDB();
        $stmt = $db->prepare("INSERT INTO products_sizes(product_id,size_id,color_id,quantity) VALUES (:productID,:sizeID,:colorID,:qnty)");
        return $stmt->execute([
            ':productID' => $productID,
            ':sizeID'   => $sideID,
            ':colorID'  => $colorID,
            ':qnty'  => $qnty
        ]); 
    }


    /**
     * Set quantity to each color of specific product 
     * Quantity value is fetched from db - sum all quantity of each product size 
     * e.g:
     * RED t-shirt
     * Size S - 10 units
     * Size M - 5 Units 
     * = 15 Units (Quantity)
     */
    public static function setProductColorQuantity($productID,$colorID)
    {
        $db = static::getDB();
        $stmt = $db->prepare("INSERT INTO products_colors(product_id,color_id,quantity) VALUES (:productID,:colorID,(SELECT sum(quantity) FROM products_sizes WHERE product_id=:productID AND color_id=:colorID))");
        return $stmt->execute([
            ':productID' => $productID,
            ':colorID'  => $colorID,
        ]); 
    }


    // Generate new product ID
    public static function generateProductID()
    {
        $db = static::getDB();
        $stmt = $db->prepare("SELECT MAX(product_id) AS newID FROM products");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result[0]['newID'] + 1;
    }


    // Get all product sizes quantites
    public static function getProductSizesQnty($productID)
    {
        $db = static::getDB();
        $stmt = $db->prepare("SELECT * FROM products_sizes WHERE product_id = :productID ORDER BY color_id ASC");
        $stmt->execute([':productID' => $productID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Update product sizes
    public static function updateProductSizes($productID,$colorID,$sizeID,$qnty)
    {
        $db = static::getDB();
        $stmt = $db->prepare("UPDATE products_sizes SET quantity = :qnty WHERE products_sizes.product_id = :productID AND products_sizes.size_id = :sizeID AND products_sizes.color_id = :colorID");
        return $stmt->execute([
            ':qnty'      => $qnty,
            ':productID' => $productID,
            ':sizeID'    => $sizeID,
            ':colorID'   => $colorID
        ]);   
    }

    
    // Update product sizes quantity after order has been set
    public static function updateProductSizesQntty($productID,$colorID,$sizeID,$qnty)
    {
        $db = static::getDB();
        $stmt = $db->prepare("UPDATE products_sizes SET quantity = :qnty WHERE products_sizes.product_id = :productID AND products_sizes.size_id = :sizeID AND products_sizes.color_id = :colorID");
        
        return $stmt->execute([
            ':qnty'      => $qnty,
            ':productID' => $productID,
            ':sizeID'    => $sizeID,
            ':colorID'   => $colorID
        ]);   
    }


    // Update colors quantities for specific product
    public static function updateProductColorQuantity($productID,$colorID)
    {
        $db = static::getDB();
        $stmt = $db->prepare("UPDATE products_colors SET quantity = (SELECT sum(quantity) FROM products_sizes WHERE product_id = :productID AND color_id = :colorID)
        WHERE products_colors.product_id = :productID AND products_colors.color_id = :colorID");
        return $stmt->execute([
            ':productID' => $productID,
            ':colorID'  => $colorID,
        ]); 
    }


    // Get low quantity products
    public static function getLowQnttyProducts()
    {
        $lastItems = Other::getValueByID(5)->string_value;
        $db= static::getDB();
        $stmt = $db->prepare("SELECT * FROM products JOIN products_colors WHERE products.product_id = products_colors.product_id GROUP BY products_colors.product_id HAVING SUM(quantity) < 5");
        $stmt->execute([':lastItems'=>$lastItems]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Get sum quantity of specific product ID
     * calculates quantity from all colors and sizes
     */
    public static function getProductColorSizeQnty($productID,$colorID,$sizeID)
    {
        $db = static::getDB();
        $stmt = $db->prepare("SELECT quantity FROM products_sizes WHERE product_id = :productID AND color_id = :colorID AND size_id = :sizeID");
        $stmt->execute([
            ':productID' => $productID,
            ':colorID'   => $colorID,
            ':sizeID'    => $sizeID
        ]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result[0]['quantity'];
    }


    // Get all product colors info - color ID,name,displayName 
    public static function getProductColors()
    {
        $db = static::getDB();
        $stmt = $db->prepare("SELECT * FROM colors ORDER BY color_id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all product sizes info - size ID,name,displayName
    public static function getProductSizes()
    {
        $db = static::getDB();
        $stmt = $db->prepare("SELECT * FROM sizes ORDER BY size_id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Get product images
    public static function getProductImgs($prodId,$colorId)
    {
        $db = static::getDB();
        $stmt = $db->prepare("SELECT * FROM products_images WHERE product_id = :prodId AND color_id = :colorId");
        $stmt->execute([':colorId'=>$colorId,':prodId'=>$prodId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Get all product images
    public static function getAllproductImgs($prodId)
    {
        $db = static::getDB();
        $stmt = $db->prepare("SELECT * FROM products_images WHERE product_id = :prodId");
        $stmt->execute([':prodId'=>$prodId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Insert product images
    public static function insertProductImg($prodId,$colorId,$imgUrl)
    {
        $db = static::getDB();
        $stmt = $db->prepare("INSERT INTO products_images(product_id,color_id,product_img) VALUES (:productID,:colorID,:imgUrl)");
        return $stmt->execute([
            ':productID' => $prodId,
            ':colorID'   => $colorId,
            ':imgUrl'  => $imgUrl
        ]); 
    }


    // Check if product img already exists
    public static function getProductImg($prodId,$colorId)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM products_images
        WHERE product_id = :prodId 
        AND color_id = :colorId');
        $stmt->execute([
            ':prodId'  => $prodId,
            ':colorId' => $colorId
        ]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }


    // Update product img
    public static function updateProductImg($prodId,$colorId,$imgUrl)
    {
        $db = static::getDB();
        $stmt = $db->prepare('UPDATE products_images SET product_img = :imgUrl WHERE product_id = :prodId AND color_id = :colorId');
        return $stmt->execute([
            ':imgUrl'  => $imgUrl,
            ':prodId'  => $prodId,
            ':colorId' => $colorId
        ]);  
    }

    // Get colors quantity
    // Check if product img already exists
    public static function getColorsQnty($prodId)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM products_colors WHERE product_id = :id');
        $stmt->execute([':id' => $prodId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
