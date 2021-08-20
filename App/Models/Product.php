<?php

namespace App\Models;

use PDO;
use \App\Models\Category;
use \App\Models\Other;



class Product extends Category
{
    /**
     * COLORS: 1 - BLUE,  2 - RED ,    3 - Black , 4 - White
     * SIZES : 1 - Small, 2 - Medium , 3 - Large , 4 - Extra large
     */

    public $errors = [];

    public $target_dir = "images/";

    public $redColorSelect   = false;
    public $blueColorSelect  = false;
    public $whiteColorSelect = false;
    public $blackColorSelect = false;



    public $NOT_FOUND_STR = "לא נמצאו תוצאות...";

    // Class constructor
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    // Insert new product to products table
    public function save()
    {
        $this->validate();
        $this->validateImages();
        if (empty($this->errors)) {
            $db = static::getDB();
            $stmt = $db->prepare("INSERT INTO products (product_id,category_id, product_name, product_price, product_img, supplier_id, description) VALUES (:productID,:cat_id, :name, :price, :img , :supp_id, :description)");
            $this->product_id = Category::generateProductID();
            $this->product_img = $_FILES['product_img']['name'];
            $this->supplier_id = Supplier::getSupplierIDByCompanyName($this->company_name)['supplier_id'];
            $result = $stmt->execute([
                ':productID'  => $this->product_id,
                ':cat_id'     => $this->category_id,
                ':name'       => $this->product_name,
                ':price'      => $this->addTaxToProductPrice($this->product_price),
                ':img'        => $this->product_img,
                ':supp_id'    => $this->supplier_id,
                ':description'=> $this->description
            ]);
            if ($result) {
                $this->setColorSizeQuantity($this->product_id,$this->colors);
                $this->saveImages($this->product_id);
                return true;
            }
        }
        return false;
    }


    // Update product information
    public function edit()
    {
        $this->validate();
        $this->validateImages();
        if (empty($this->errors)) {
            $db = static::getDB();
            $stmt = $db->prepare('UPDATE products SET category_id=:cat_id,product_name=:name,product_price=:price,product_img=:img,supplier_id=:supp_id,description=:description WHERE product_id = :id');
            $this->product_img = $_FILES['product_img']['name'] ?? $this->product_img;
            $this->supplier_id = Supplier::getSupplierIDByCompanyName($this->company_name)['supplier_id'];
            $result = $stmt->execute([
                ':cat_id'     => $this->category_id,
                ':name'       => $this->product_name,
                ':price'      => $this->addTaxToProductPrice($this->product_price),
                ':img'        => $this->product_img,
                ':supp_id'    => $this->supplier_id,
                ':description'=> $this->description,
                ':id'         => $this->product_id
            ]);     
            if ($result) {
                $this->updateColorSizeQuantity($this->product_id,$this->colors);
                $this->saveImages($this->product_id);
                return true;
            }      
        }
        return false;
    }


    // Add taxes to added products
    public function addTaxToProductPrice($price)
    {
        $taxPrecentage = Other::getValueByID(11)->string_value;
        return $price * ($taxPrecentage / 100 + 1);
    }

    
    // Set quantity for each color and size of specific product
    public function setColorSizeQuantity($productID,$colors)
    {
        for ($colorID = 1; $colorID <= count($colors); $colorID++) {
            for ($sizeID = 1; $sizeID <= count($colors[$colorID - 1]); $sizeID++) {
                Category::setProductSizeQuantity($productID,$sizeID,$colorID,$colors[$colorID - 1][$sizeID - 1]);
            }
        }
        for ($colorID = 1; $colorID <= 4; $colorID++) {
            Category::setProductColorQuantity($productID,$colorID);
        }
    }


    // Update quantity for each color and size of specific product
    public function updateColorSizeQuantity($productID,$colors)
    {
        foreach ($colors as $colorID => $v) {
            foreach ($v as $sizeID => $qnty) {
                Category::updateProductSizes($productID,$colorID + 1,$sizeID + 1,$qnty);
            }
        }
        for ($colorID = 1; $colorID <= 4; $colorID++) {
            Category::updateProductColorQuantity($productID,$colorID);
        }
    }

    // Building colors => sizes => quantity array 
    public function buildColorsSizesArray($colors)
    {
        $colorsAndSizes = [];
        foreach($colors as $v) {
            $colorsAndSizes[$v['color_id'] - 1][$v['size_id'] - 1] = $v['quantity'];
        }
        $this->colors = $colorsAndSizes;
    }
    

    public function buildProdImgsArray($images)
    {
        $colorAndImgs = [];
        foreach ($images as $img) {
            $colorId = $img['color_id'];
            $colorAndImgs[] = [
                'colorId'=> $colorId,
                'imgUrl' => $img['product_img']
            ];
            if ($colorId == '1') $this->blueColorSelect  = true;  // Blue color
            if ($colorId == '2') $this->redColorSelect   = true;  // Red color
            if ($colorId == '3') $this->blackColorSelect = true;  // Black color
            if ($colorId == '4') $this->whiteColorSelect = true;  // White
        }
        $this->prodsColorImgs = $colorAndImgs;
    }

    // Validate product information
    public function validate()
    {
        if (empty($this->product_name)) {
            $this->errors[] = "יש להזין שם מוצר";
        }
        if (strlen($this->product_name) > 25) {
            $this->errors[] = "הזנת שם מוצר ארוך מידי";
        }
        if (! static::isCategoryExists($this->category_id)) {
            $this->errors[] = "הקטגוריה שבחרת אינה קיימת";
        }
        if (empty($this->product_price)) {
            $this->errors[] = "יש להזין מחיר מוצר";
        }
        if ($this->product_price < 0) {
            $this->errors[] = "מחיר המוצר אינו יכול להיות שלילי";
        }
        if (empty($this->description)) {
            $this->errors[] = "תיאור המוצר אינו יכול להיות ריק";
        }
        if (! $this->validateColorsSizes()) {
            $this->errors[] = "עלייך למלא את הכמויות של כל המידות הקיימות";
        }
        if (!Supplier::getSupplierIDByCompanyName($this->company_name)) {
            $this->errors[] = "עלייך להוסיף ספק";
        }
    }


    // Check if all quantity sizes are set & quantity >= 0
    public function validateColorsSizes()
    {
        foreach ($this->colors as $color) {
            foreach ($color as $size) {
                if ($size == "" || $size < 0) {
                    return false;
                }
            }
        }
        return true;
    }


    // Check if category_id is exists
    public static function isCategoryExists($id)
    {
        return Category::getCategoryByID($id);
    }






    /**
     * Product Image functions
     */
    public function validateImages()
    {
        if (isset($_FILES['product_img'])) {
            $this->validateImg($_FILES['product_img'],"תמונה ראשית");
        }
        if (isset($_FILES['product_img_black'])) {
            $_FILES['product_img_black']['color_id'] = 3;
            $this->validateImg($_FILES['product_img_black'],"תמונה לצבע השחור");
            $this->blackColorSelect = true;
        }
        if (isset($_FILES['product_img_red'])) {
            $this->validateImg($_FILES['product_img_red'],"תמונה לצבע האדום");
            $_FILES['product_img_red']['color_id'] = 2;
            $this->redColorSelect = true;
        }
        if (isset($_FILES['product_img_blue'])) {
            $this->validateImg($_FILES['product_img_blue'],"תמונה לצבע הכחול");
            $_FILES['product_img_blue']['color_id'] = 1;
            $this->blueColorSelect = true;
        }
        if (isset($_FILES['product_img_white'])) {
            $this->validateImg($_FILES['product_img_white'],"תמונה לצבע הלבן");
            $_FILES['product_img_white']['color_id'] = 4;
            $this->whiteColorSelect = true;
        }
    }


    public function saveImgLocal($imgFile)
    {
        $imgUrl = str_replace(' ','',$imgFile['name']);
        $targetFile = $this->target_dir . basename($imgUrl);
        return move_uploaded_file($imgFile['tmp_name'], $targetFile);
    }

    
    public function saveImages($prodId)
    {
        $imgFiles = $_FILES;
        if (isset($_FILES['product_img'])) {
            $mainImg = array_shift($imgFiles);
            $this->saveImgLocal($mainImg);
        }
        foreach ($imgFiles as $img) {
            if (array_key_exists('color_id', $img)) {
                if ($this->saveImgLocal($img)) {
                    if (! $this->imgAlreadyExists($prodId,$img['color_id'])) {
                        Category::insertProductImg($prodId,$img['color_id'],$img['name']);
                    }else {
                        Category::updateProductImg($prodId,$img['color_id'],$img['name']);
                    }
                }
            }
        }
    }


    // Check if img exists
    public function imgAlreadyExists($prodId,$colorId)
    {
        return Category::getProductImg($prodId,$colorId);
    }


    // Validate uploaded image
    public function validateImg($imgFile,$imgDesc)
    {
        $targetFile = $this->target_dir . basename($imgFile['name']);
        $imageFileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));

        if (!empty($imgFile['name'])) {
            if (!getimagesize($imgFile['tmp_name'])) {
                $this->errors[] = "$imgDesc שהכנסת אינה תקינה";
            }
            if (file_exists($targetFile)) {
                $this->errors[] = "$imgDesc שהכנסת כבר קיימת";
            }
            if (!$this->isValidType($imageFileType)) {
                $this->errors[] = "הקובץ שהעלאת אינו $imgDesc";
            }
        }else {
            $this->errors[] = "עלייך להוסיף $imgDesc";
        }  
    }


    
    // Check if file type is one of the following
    public function isValidType($imageFileType)
    {
        if($imageFileType != "jpg" 
        && $imageFileType != "png" 
        && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            return false;
        }
        return true;
    }


    // Create products table from products array
    public static function createProductsTable($products)
    { 
        $tableRows = "";
        foreach($products as $p) 
        {
            $pid= $p['product_id'];
            $dn = $p['display_name'];
            $pid= $p['product_id'];
            $pn = $p['product_name'];
            $pp = $p['product_price'];
            $pq = $p['product_quantity'];
            $ua = $p['uploaded_at'];
            $pi = $p['product_img'];
            $pd = $p['description'];

            $tableRows .= "<tr>
            <th scope='row'>$pid</th>
            <td>$dn</td>
            <td>$pn</td>
            <td>$pp</td>
            <td>$pq</td>
            <td>$ua</td>
            <!--Product row buttons-->
            <td>
                <div class='d-flex justify-content-end'>
                    <form action='/admin/products/delete/$pid' method='POST'>
                        <button class='btn btn-primary mr-2' name='deleteProduct'>הסרה</button>
                    </form>
                    <a href='/admin/products/edit/$pid' class='btn btn-primary mr-2'>עדכון</a>
                    <div class='d-flex align-items-end'>
                        <a href='#product_info$pid' class='btn btn-default' data-toggle='collapse'>
                            <svg class='bi bi-chevron-compact-down' width='1em' height='1em' viewBox='0 0 16 16'
                                fill='currentColor' xmlns='http://www.w3.org/2000/svg'>
                                <path fill-rule='evenodd'
                                    d='M1.553 6.776a.5.5 0 01.67-.223L8 9.44l5.776-2.888a.5.5 0 11.448.894l-6 3a.5.5 0 01-.448 0l-6-3a.5.5 0 01-.223-.67z'
                                    clip-rule='evenodd' />
                            </svg>
                        </a>
                    </div>
                </div>
            </td>
        </tr>
        <tr id='product_info$pid' class='main-container collapse' >
            <td colspan='7'>
                <div class='d-flex'>
                    <div class='w-25'>
                        <img src='/images/$pi' alt='$dn' class='img-thumbnail'>
                    </div>
                    <div class='pl-4'>
                        <span class='h6'>תיאור המוצר</span>
                        <div>
                        $pd
                        </div>
                    </div>
                </div>
            </td>
            
        </tr>";
        }
        return $tableRows;
    }
}







