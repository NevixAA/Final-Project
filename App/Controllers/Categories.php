<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Category;
use \App\Models\Other;



class Categories extends \Core\Controller
{
    static $RELATED_PRODUCTS = 4; // Limit related products cards


    // Show the index page
    public function indexAction()
    {
        $products = Category::getAllProducts();

        View::renderTemplate('Categories/index.html',[
            'products' => $products,
            'almost_gone' => $this->getAllowedMaxQnty()
        ]);
    }


    // Show specific category page, by category name from URL
    public function findByName()
    {
        $category = Category::getCategoryByName($this->route_params['name']);
        if ($category) {
            $products = Category::getProductByCategoryID($category->category_id);
            if ($products) {
                View::renderTemplate('Categories/index.html',[
                    'products' => $products,
                    'categoryName' => $category->display_name,
                    'almost_gone' => $this->getAllowedMaxQnty()
                ]);
            }else {
                $this->redirect('/categories');
            }
        } else {
            $this->redirect('/categories'); // If category page not found, take visitor to categories index page
        }
    }


    // Show view product page
    public function getProductById()
    {
        $product = Category::getProduct(Category::getCategoryByName($this->route_params['name'])->category_id,$this->route_params['id']);
        
        if ($product) {
            $relatedProducts = Category::getRelatedProducts($product[0]['category_id'],$product[0]['product_id'],Categories::$RELATED_PRODUCTS);
            $colorsQnty = Category::getColorsQnty($product[0]['product_id']);
            View::renderTemplate('Categories/view_product.html',[
                'product' => $product,
                'related_products' => $relatedProducts,
                'colorsQnty' => $colorsQnty
            ]);
        }else {
            $this->redirect('/categories');
        }
    }


    // Change product image after picking color and size
    public function getImgsByColorSizeId()
    {
        $colorId = json_decode($_POST['colorId']);
        $prodId  = json_decode($_POST['prodId']);
        $img     = Category::getProductImgs($prodId,$colorId);
        echo $img[0]['product_img'];
    }

    
    //Get the maximum quantity allowed to add to cart
    public function getAllowedMaxQnty()
    {
        return Other::getValueByID(17)->string_value;
        
    }

}



