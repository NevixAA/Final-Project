<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Flash;
use \App\Models\Category;
use \App\Models\Product;
use \App\Models\Supplier;
use \App\Models\Order;
use \App\Controllers\Admin\AdminAuthenticated;


class ProductsController extends AdminAuthenticated
{
    public $errors = [];


    // Show all products in product table
    public function indexAction()
    {
        $products       = Category::getAllProducts();
        $category       = Category::getAllCategories();
        $sizesQuantity  = Category::getProductsSizesQuantity();
        $colors         = Category::getColors();

        View::renderTemplate('Admin/products/index.html',[
            'categories'    => $category ?? null,
            'products'      => $products ?? null,
            'colors'        => $colors ?? null,
            'SizesQuantity' => $sizesQuantity ?? null,
            'textErrors'    => $this->errors['text_errors'] ?? null
        ]);
    }


    // Update product action
    public function editAction()
    {
        $categories = Category::getAllCategories();
        $suppliers = Supplier::getAllSuppliers();
        $productModel = new Product(Category::getProductByID($this->route_params['id'])[0]);
        $productModel->buildColorsSizesArray(Category::getProductSizesQnty($productModel->product_id));
        $productModel->buildProdImgsArray(Category::getAllproductImgs($productModel->product_id));
        if (isset($_POST['product_updated'])) {
            $editedProduct = new Product($_POST);
            $editedProduct->redColorSelect   = $productModel->redColorSelect;
            $editedProduct->blackColorSelect = $productModel->blackColorSelect;
            $editedProduct->whiteColorSelect = $productModel->whiteColorSelect;
            $editedProduct->blueColorSelect  = $productModel->blueColorSelect;
            $editedProduct->product_img      = $productModel->product_img;
            if ($editedProduct->edit()) {
                Flash::AddMessage("המוצר התעדכן בהצלחה",Flash::INFO);
                $this->redirect('/admin/products');
            }else {
                $this->renderProduct($editedProduct,$categories,$suppliers,true);
            }
        }else {
            $this->renderProduct($productModel,$categories,$suppliers,true);
        }   
    }


    // Add product action
    public function createAction()
    {
        $categories = Category::getAllCategories();
        $suppliers = Supplier::getAllSuppliers();
        if (isset($_POST['product_added'])) {
            $productModel = new Product($_POST);
            if ($productModel->save()) {   // Validate and insert product to products table
                Flash::AddMessage("המוצר התווסף בהצלחה",Flash::SUCCESS);
                $this->redirect('/admin/products');
            }else {
                $this->renderProduct($productModel,$categories,$suppliers,false); 
            }
        }else {
            $this->renderProduct(null,$categories,$suppliers,false);
        }   
    }


    // Delete single product
    public function deleteAction()
    {
        if (isset($_POST['deleteProduct'])) {
            $product = Category::getProductByID($this->route_params['id']);
            $isOrderSent = Order::isOrderSent($this->route_params['id']);
            if($product && $isOrderSent) {
                Category::deleteProduct($product[0]['product_id']);
                Flash::AddMessage("המוצר נמחק בהצלחה",Flash::SUCCESS);
            }else if (!$isOrderSent) {
                Flash::AddMessage("המוצר קיים בהזמנה ולא ניתן להסירו",Flash::WARNING);
            }
        }
        $this->redirect('/admin/products');
    }


    // Render add/edit product page with categories, product model
    public function renderProduct($product,$categories,$suppliers,$editProduct)
    {
        View::renderTemplate('Admin/products/edit_product.html',[
            'editProduct'      => $editProduct,
            'categories'       => $categories ?? null,
            'product'          => $product ?? null,
            'colors'           => $product->colors ?? null,
            'suppliers'        => $suppliers ?? null,
            'prodsImgs'        => $product->prodsColorImgs ?? null,
            'redColorSelect'   => $product->redColorSelect ?? false,
            'blackColorSelect' => $product->blackColorSelect ?? false,
            'whiteColorSelect' => $product->whiteColorSelect ?? false,
            'blueColorSelect'  => $product->blueColorSelect ?? false
        ]);
    }











    /**
     * Text functions
     */
    // Export products to txt file
    public function exportTxtAction()
    {
        if (isset($_POST['txt_file_name']))
        {
            $this->validateTxtFileName($_POST['txt_file_name']);
            if (empty($this->errors['text_errors'])) {
                $productsList = Category::getAllProducts();
                if ($productsList) {
                    $usersString = "";
                    foreach ($productsList as $k=>$p) { // Users array toString
                        $usersString .= implode(" ",$p) . "\n";
                    }
                    file_put_contents("../App/exports/txt/".$_POST['txt_file_name'].".txt",$usersString);
                    Flash::addMessage('הקובץ נוצר בהצלחה!',Flash::SUCCESS);
                    $this->redirect('/admin/products');
                }
            }
        }
        $this->indexAction(); // Load admin homepage 
    }


    /**
     * Validate file_name string
     * Errors saves in errors['textErrors']
     */
    public function validateTxtFileName($fileName)
    {
        if (empty($fileName))                       // Check if fileName empty
            $this->errors['text_errors'][] = "שם הקובץ שהכנסת לא תקין";
        
        if (strpos($fileName,' ') !== false)        // Check if fileName contains spaces
            $this->errors['text_errors'][] = "שם הקובץ אינו יכול להכיל רווחים";
        
        if (preg_match("/[^A-Za-z0-9]/", $fileName)) // Check if file name contains other charachters besides A-Z, a-z, 0-9
            $this->errors['text_errors'][] = "יש להכניס אותיות באנגלית ומספרים בלבד";

    }








    /**
     * Search functions
     */
    // Response to AJAX call to search product by id
    public function searchIDAction()
    {
        $productID = $_POST['productID'];
        if (empty($productID)) {             
            echo Product::createProductsTable(Category::getAllProducts());          
        }else {
            // If product found echo product details, else echo NotFound string
            echo empty($product = Category::getProductByID($productID)) ?  "<tr><td colspan=7>לא נמצאו תוצאות בחיפוש לפי מזהה...</td></tr>" : Product::createProductsTable($product);
        }
    }


    // Response to AJAX call to search product by category
    public function searchCategoryAction()
    {
        $categoryID = $_POST['categoryID'];
        if (empty($categoryID)) {            
            echo Product::createProductsTable(Category::getAllProducts());          
        }else {
            // If product found echo product details, else echo NotFound string
            echo empty($products = Category::getProductByCategoryID($categoryID)) ?  "<tr><td colspan=7>לא נמצאו תוצאות בחיפוש לפי קטגוריה...</td></tr>" : Product::createProductsTable($products);
        }
    }

    
    // Response to AJAX call to search product by name
    public function searchNameAction()
    {
        echo json_encode([
            'products' => Category::getAllProducts(),
            'colors' => Category::getColors(),
            'sizesQnty' => Category::getProductsSizesQuantity()
        ]);


    }

}