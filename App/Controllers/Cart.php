<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Category;
use \App\Models\Other;
use \App\Models\Order;
use \App\Models\Order_Products;
use \App\Models\Color;
use \App\Models\Size;
use \App\Models\Discount;

class Cart extends Authenticated
{
    public $SHIPPING_PRICE;

    public $TOTAL_PRICE = 0;

    public $SUBTOTAL_PRICE;

    public $DISCOUNT = [];


    /**
     * Shopping cart index page
     */
    public function indexAction()
    {      
        $this->calcFinalPrices();
        if (isset($_SESSION['cart_items'])) {
            $cart_items = $_SESSION['cart_items'];
        }
        View::renderTemplate('Cart/index.html',[
            'cart_items'           => $cart_items ?? null,
            'subTotal_price'       => $this->SUBTOTAL_PRICE,
            'shipping_price'       => $this->SHIPPING_PRICE,
            'total_price'          => $this->TOTAL_PRICE,
            'discount'             => $this->DISCOUNT ?? null,
            'shipping_free_str'    => $this->SHIPPING_FREE_STR ?? null,
            'shipping_price_dscnt' => $this->SHIPPING_PRICE_DISCOUNT ?? null
        ]);
    }

    
    // Check discounts for cart items
    public function checkForDiscounts()
    {
        $this->DISCOUNT = Discount::findDiscounts();
        return $this->DISCOUNT['value'] ?? 0;  
    }


    /**
     * Set the order event
     */
    public function successAction()
    {
        Flash::AddMessage("הזמנתך נקלטה בהצלחה",Flash::INFO);
        $this->calcFinalPrices();
        View::renderTemplate('Cart/success.html',[
             'cart_items'     => $_SESSION['cart_items'] ?? null,
             'shipping_price' => $this->SHIPPING_PRICE,
             'totalprice'     => $this->TOTAL_PRICE
        ]);
    }


    /**
     * Add to cart action
     */
    public function addAction()
    {   
        header('Content-Type: application/json');
        if (!array_key_exists('cart_items',$_SESSION)) {     // Init cart_items array
            $_SESSION['cart_items'] = [];
        }
        if (isset($_POST['addToCart'])) {
            $product = json_decode($_POST['addToCart'],true);
            if ($this->IsProductInStock($product['id'],$product['color'],$product['size'])) {
                // Check if product already exists in cart
                if ($this->checkIfProductExistsInCart($product['id'],$product['color'],$product['size']) != false) { 
                    $this->updateProductInCart($product);
                    echo json_encode("updated");
                }else { 
                    $this->addNewProductToCart($product);  
                    echo json_encode("added");  
                }
            }else {
                echo json_encode("out_of_stock");
            }
        }
    }


    /**
     * Remove specific product from cart
     */
    public function removeProductFromCart()
    {
        if (isset($_POST['product_info'])) {
            $productInfo = explode('-',$_POST['product_info']);
            unset($_SESSION['cart_items']['id'.$productInfo[0].'-color'.$productInfo[1].'-size'.$productInfo[2]]);
        }
        $this->redirect('/cart');
    }

    
    /**
     * Check if product is in stock
     */
    public function IsProductInStock($productID,$colorID,$sizeID)
    {
        $productQnty = Category::getProductColorSizeQnty($productID,$colorID,$sizeID);
        return $productQnty > 0 ? true : false;
    }


    /**
     * Check if product exists in shopping cart($_SESSION)
     * RETURN:
     * Product key in $_SESSION['cart_items'] array
     * False - if product not found
     */
    public function checkIfProductExistsInCart($productID,$colorID,$sizeID)
    {
        $searchKey = `id$productID-color$colorID-size$sizeID`;
        foreach($_SESSION['cart_items'] as $k => $v) {      // Search for productID in cart_items
            if ($searchKey == $k) {
                return $k;
            }
        }
        return false;
    }


    /**
     * Add new product to shopping cart
     */
    public function addNewProductToCart($product)
    {
        $colorID = $product['color'];
        $productID = $product['id'];
        $sizeID = $product['size'];
        $qnty = $product['qnty'];
        $product     = Category::getProductByID($productID);
        $productQnty = Category::getProductColorSizeQnty($productID,$colorID,$sizeID);
        if ($product && $productQnty) {
            foreach($product[0] as $k => $v)                                     // Build single product array
                $singleProduct[$k]=$v;
            $singleProduct['quantity'] = $qnty >= $productQnty ? $productQnty : $qnty;    
            $singleProduct['color']    = $colorID;
            $singleProduct['colorDisplayName'] = $this->getProductColorDisplayName($colorID);
            $singleProduct['size']     = $sizeID;
            $singleProduct['sizeDisplayName']  = $this->getProductSizeDisplayName($sizeID);
            $singleProduct['total']    = $singleProduct['product_price'] * $singleProduct['quantity']; // Calc Total = Product Quantity * Price 
            $singleProduct['product_img'] = Category::getProductImgs($singleProduct['product_id'],$colorID)[0]['product_img'];
            $_SESSION['cart_items'][ 'id'.$productID.'-color'.$colorID.'-size'.$sizeID ] = $singleProduct;
        }
    }


    /**
     * Response to AJAX Request
     * Send the maximum quantity allowed to add to cart
     */
    public function getAllowedMaxQnty()
    {
        echo json_encode(Other::getValueByID(17)->string_value);
    }


    /**
     * Get product color displayName
     * Search for the displayName using colorID
     */
    public function getProductColorDisplayName($colorID)
    {
        $colorsArray = Category::getProductColors();
        if ($colorsArray) {
            foreach($colorsArray as $color) {
                if ($color['color_id'] == $colorID) {
                    return $color['display_name'];
                }
            }
        }
        return false;

    }


    /**
     * Get product size displayName
     * Search for the displayName using sizeID
     */
    public function getProductSizeDisplayName($sizeID)
    {
        $sizesArray = Category::getProductSizes();
        if ($sizesArray) {
            foreach($sizesArray as $size) {
                if ($size['size_id'] == $sizeID) {
                    return $size['display_name'];
                }
            }
        }
        return false;
    }


    /**
     * Update product quantity in shopping cart
     */
    public function updateProductInCart($product)
    {
        $colorID = $product['color'];
        $productID = $product['id'];
        $sizeID = $product['size'];
        $qnty = $product['qnty'];

        $key = `id$productID-color$colorID-size$sizeID`;

        $productQnty = Category::getProductColorSizeQnty($productID,$colorID,$sizeID);

        if ($_SESSION['cart_items'][$key]['quantity'] + $qnty > $productQnty) {
            $_SESSION['cart_items'][$key]['quantity'] = $productQnty;           // If product quantity is over product stock, set quantity in cart = max
        }else {
            $_SESSION['cart_items'][$key]['quantity'] += $qnty;
        }   // Calculate Total = Product Quantity * Price 
        $_SESSION['cart_items'][$key]['total'] = $_SESSION['cart_items'][$key]['quantity'] * $_SESSION['cart_items'][$key]['product_price'];
      
    }


    /**
     * Get product Quantity
     * Response to AJAX Request
     */
    public function isProductInStockAJAX()
    {
        header('Content-Type: application/json');
        if (isset($_POST['product'])) {
            $product = json_decode($_POST['product'],true);
            $colorID = $product['color'];
            $productID = $product['id'];
            $sizeID = $product['size'];
            if ($this->IsProductInStock($productID,$colorID,$sizeID)) {
                echo json_encode('true');
                return;
            }
        }
        echo json_encode("false");
    }

    
    /**
     * Clear all products from shopping cart
     */
    public function clearAction()
    {
        if (isset($_POST['clear_cart'])) {
            unset($_SESSION['cart_items']);
        }
        Flash::AddMessage("ניקית את העגלה בהצלחה",Flash::INFO);
        $this->redirect('/cart');  
    }


    /**
     * Calculate cart product total price without shipping
     */
    public function calcTotalPrice($cartProducts)
    {
        $total = 0;
        foreach ($cartProducts as $p) {
            $total += $p['total'];
        }
        return $total;
    }

    /**
     * Response to AJAX call to get all shopping cart products
     */
    public function getCartProducts()
    {
        header('Content-Type: application/json');
        $cartItems = $_SESSION['cart_items'];
        $totalPrice = $this->calcTotalPrice($cartItems); // Add total products price 
        $cartItems[] = array('totalItemsPrice'=>$totalPrice);
        echo json_encode($cartItems);
    }

    /**
     * Response to AJAX call
     * Find product by product ID
     * Return product quantity
     */

    public function findProductQntty()
    {
        header('Content-Type: application/json');
        if (isset($_POST['product'])) {
            $product = json_decode($_POST['product'],true);
            $id      = $product['id'];
            $colorID = $product['color'];
            $sizeID  = $product['size'];
            echo json_encode(Category::getProductColorSizeQnty($id,$colorID,$sizeID));
        }else {
            echo json_encode("error");
        }
    }


    /**
     * Response to AJAX call
     * Update products in cart ($_SESSION['cart_items'])
     */
    public function updateProductQntty()
    {
        if (isset($_SESSION['cart_items']) && isset($_POST['updatedProducts']) ) {
            $updatedProducts = json_decode($_POST['updatedProducts'],true);
            foreach($updatedProducts as $key => $product) {
                $info = explode('-',$key);   //productID-colorID-sizeID 
                $newKey = 'id'.$info[0].'-color'.$info[1].'-size'.$info[2];
                $_SESSION['cart_items'][$newKey]['quantity'] = $product['qnty'];
                $_SESSION['cart_items'][$newKey]['total']    = $product['qnty'] * $_SESSION['cart_items'][$newKey]['product_price'];
            }
            echo $this->calcTotalPrice($_SESSION['cart_items']);
        }
    } 


    /**
     * Set the order event
     */
    public function setAction()
    {
        $orderID       = Order::generateOrderID();
        $Today         = date('y:m:d');
        $DaysToDeliver = $this->getEstimatedDeliveryDays();
        $NewDate       = Date('y:m:d', strtotime('+'.$DaysToDeliver.'days'));
        $this->buildOrderDetails($orderID, $Today, $NewDate);   // build and save order for order table
        $this->buildOrderProductsDetails($orderID);             // build and save order for order_products table
        $this->updateSoldProductsQuantity();
        echo json_encode("true");
    }
    

    /**
     * Build details for order table
     */
    public function buildOrderDetails($orderID,$today,$expDate)
    {
        $totalPrice = $this->calcTotalPrice($_SESSION['cart_items']);
        $order = new Order($orderID, $_SESSION['user_id'], $today, $expDate, $totalPrice); // set new order before save it on the db
        $order->save();
    }


    /**
     * Build details for order_products table
     */
    public function buildOrderProductsDetails($orderID)
    {
        foreach($_SESSION['cart_items'] as $k => $v) {
            $order_products = new Order_Products($orderID,$v['product_id'],$v['color'],$v['size'],$v['product_price'],$v['quantity']);// set new order_products before save it on the db
            $order_products->save();
        }
    }
    

    /**
     * Update quantity of products that has been ordered
     */
    public function updateSoldProductsQuantity()
    {
        foreach($_SESSION['cart_items'] as $k=>$v) {
           $qnty = Category::getProductColorSizeQnty($v['product_id'],$v['color'],$v['size']);
           Category::updateProductSizesQntty($v['product_id'],$v['color'],$v['size'],$qnty - $v['quantity']);
           Category::updateProductColorQuantity($v['product_id'],$v['color']);
        }
    }

    


    /**
     * Calculate shipping,subtotal,total prices
     */
    public function calcFinalPrices()
    {
        $this->SHIPPING_PRICE = $this->getShippingPrice();
        $this->SUBTOTAL_PRICE = 0;
        if (isset($_SESSION['cart_items'])) {
            $this->SUBTOTAL_PRICE = $this->calcTotalPrice($_SESSION['cart_items']);
        }
        $this->findFreeShipping();
        $this->TOTAL_PRICE = $this->SUBTOTAL_PRICE + $this->SHIPPING_PRICE - $this->checkForDiscounts();
    }


    // Determine if user
    public function findFreeShipping()
    {
        $result = Discount::checkForFreeShipping($this->SUBTOTAL_PRICE);
        if ($result != false ) {
            $this->SHIPPING_PRICE_DISCOUNT = $this->SHIPPING_PRICE;
            $this->SHIPPING_FREE_STR = $result;
            $this->SHIPPING_PRICE = 0;
        }
    }


    // Get shipping price
    public function getShippingPrice()
    {
        return Other::getValueByID(3)->string_value;
    }

    

    // Get delivery estimated days
    public function getEstimatedDeliveryDays()
    {
        $value = Other::getValueByID(2);
        return $value->string_value;
    }


    // Get all cart items total price
    public function getCartItemsTotalPrice()
    {
        $this->calcFinalPrices();
        echo json_encode($this->TOTAL_PRICE);
    }
}
