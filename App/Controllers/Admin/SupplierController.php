<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Flash;
use \App\Models\Supplier;




class SupplierController extends AdminAuthenticated
{
    public $errors = [];


    // Show all suppliers in suppliers table
    public function indexAction()
    {
        $suppliers = Supplier::getAllSuppliers();
        View::renderTemplate('Admin/suppliers/index.html',[
            'suppliers' => $suppliers ?? null,
            'textErrors' => $this->errors['text_errors'] ?? null
        ]);

    }

    // Add supplier action
   public function createAction()
    {
        if (isset($_POST['supplier_added'])) 
        {
            $supplierModel = new Supplier($_POST);
                if ($supplierModel->save()) {   // Validate and insert supplier to suppliers table
                    $this->redirect('/admin/suppliers');
                }else {
                    $this->renderAddSupplier($supplierModel); 
                }
        }else {
                $this->renderAddSupplier(null);
        }   
    }

    // Update supplier action
    public function editAction()
    {
        $suppliersFromDB = Supplier::getSupplierByID($this->route_params['id']);
        if (isset($_POST['supplier_updated'])) 
        {
            $supplierModel = new Supplier($_POST);
            $supplierModel->supplier_id = $suppliersFromDB[0]['supplier_id'];
            if ($supplierModel->edit()) {
                Flash::addMessage('פרטי הספק עודכנו בהצלחה', Flash::SUCCESS);
                $this->redirect('/admin/suppliers');
            }else {
                Flash::addMessage('אחד מהפרטים שהזנת שגויים', Flash::WARNING);
                $this->renderEditSupplier($supplierModel);
            }
        }else {
            $this->renderEditSupplier($suppliersFromDB[0]);
        }   
    }

    // Render Edit specific supplier page 
    public function renderEditSupplier($supplier)
    {
        View::renderTemplate('Admin/suppliers/edit_suppliers.html',[
            'supplier' => $supplier ?? null
        ]);
    }


    // Render add supplier page with suppliers, supplier model
    public function renderAddSupplier($supplier)
    {
        View::renderTemplate('Admin/suppliers/add_supplier.html',[
            'supplier' => $supplier ?? null
        ]);
    }

    // Delete single supplier
    public function deleteAction()
    {
        echo $_POST['deleteSupplier'];
         if (isset($_POST['deleteSupplier'])) {
             $supplier = Supplier::getSupplierByID($this->route_params['id']);
             if ($supplier) {
                   Supplier::deleteSupplier($supplier[0]['supplier_id']);
             }
        }
        $this->redirect('/admin/suppliers');
    }


    

    // Export supliers to txt file
    public function exportTxtAction()
    {
        if (isset($_POST['txt_file_name']))
        {
            $this->validateTxtFileName($_POST['txt_file_name']);
            if (empty($this->errors['text_errors'])) 
            {
                $suppliersList = Supplier::getAllSuppliers();
                if ($suppliersList)
                 {
                    $SupplierString = "";
                    foreach ($suppliersList as $k=>$p) { // Users array toString
                        $SupplierString .= implode(" ",$p) . "\n";
                    }
                    file_put_contents("../App/exports/txt/".$_POST['txt_file_name'].".txt",$SupplierString);
                    Flash::addMessage('הקובץ נוצר בהצלחה!',Flash::SUCCESS);
                    $this->redirect('/admin/suppliers');
                }
            }
        }
        $this->indexAction(); // Load admin homepage 
    }



    // Validate file_name string
    // Errors saves in errors['textErrors']
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
     * Search in suppliers
     */

    //Response to AJAX call to search Suppliers by company_name
    public function searchCompanyNameAction()
    {
        $supplierID = $_POST['supplier_id'];
        if (empty($supplierID)) {       
            echo Supplier::createSuppliersTable(Supplier::getAllSuppliers());          
        }else {
            // If supplier found echo supplier details, else echo NotFound string
            echo empty($supplier = Supplier::getSupplierByID($supplierID)) ?  "<tr><td colspan=7>לא נמצאו תוצאות בחיפוש לפי שם חברה...</td></tr>" : Supplier::createSuppliersTable($supplier);
        }
    }


    // Response to AJAX call to search Suppliers by supplier id
    public function searchIDAction()
    {
        $supplierID = $_POST['supplier_id'];
        if (empty($supplierID)) {                
            echo Supplier::createSuppliersTable(Supplier::getAllSuppliers());          
        }else {
            // If supplier found echo supplier details, else echo NotFound string
            echo empty($supplier = Supplier::getSupplierByID($supplierID)) ?  "<tr><td colspan=7>לא נמצאו תוצאות בחיפוש לפי מזהה ספק...</td></tr>" : Supplier::createSuppliersTable($supplier);
        }
    }
        
    // Response to AJAX call to search Suppliers  contact name
    public function searchContactNameAction()
    {
        $contactName = $_POST['contact_name'];
        if (empty($contactName)) {            
            echo Supplier::createSuppliersTable(Supplier::getAllSuppliers());          
        }else {
            // If supplier found echo supplier details, else echo NotFound string
            echo empty($supplier = Supplier::getSupplierByContactName($contactName)) ?  "<tr><td colspan=7>לא נמצאו תוצאות בחיפוש לפי שם ספק...</td></tr>" : Supplier::createSuppliersTable($supplier);
        }
    }


    // Response to AJAX call to search Suppliers  contact name
    public function searchEmailAction()
    {
        $email = $_POST['email'];
        if (empty($email)) {                 
            echo Supplier::createSuppliersTable(Supplier::getAllSuppliers());          
        }else {
            // If supplier found echo supplier details, else echo NotFound string
            echo empty($supplier = Supplier::getSupplierByEmail($email)) ?  "<tr><td colspan=7>לא נמצאו תוצאות בחיפוש לפי שם ספק...</td></tr>" : Supplier::createSuppliersTable($supplier);
        }
    }
    

}