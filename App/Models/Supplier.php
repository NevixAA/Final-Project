<?php

namespace App\Models;

use PDO;


class Supplier extends \Core\Model
{
    public $errors = [];

    // Class constructor
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }


    // Insert new supplier to suppliers table
    public function save()
    {
        $this->validate();
        $this->supplier_id = Supplier::generateSupplierID();
        if (empty($this->errors)) {
            $db = static::getDB();
            $stmt = $db->prepare("INSERT INTO suppliers (supplier_id,company_name, contact_name, contact_title, contact_phone, company_phone, supplier_email) VALUES (:supplier_id, :comp_name, :cont_name, :title, :cont_phone, :comp_phone, :email)");
            $stmt->bindValue(':supplier_id', $this->supplier_id, PDO::PARAM_INT);
            $stmt->bindValue(':comp_name', $this->company_name, PDO::PARAM_STR);
            $stmt->bindValue(':cont_name', $this->contact_name, PDO::PARAM_STR);
            $stmt->bindValue(':title', $this->contact_title, PDO::PARAM_STR);
            $stmt->bindValue(':cont_phone', $this->contact_phone, PDO::PARAM_STR);
            $stmt->bindValue(':comp_phone', $this->company_phone, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->supplier_email, PDO::PARAM_STR);

            return $stmt->execute();
        }
        return false;
    }

    // Edit specific supplier
    public function edit()
    {
        $this->validate();
        if (empty($this->errors)) {
            $db = static::getDB();
            $stmt = $db->prepare('UPDATE suppliers SET company_name=:comp_name,contact_name=:cont_name,contact_title=:title,contact_phone=:cont_phone,company_phone=:comp_phone,supplier_email=:email WHERE supplier_id = :id');
            $stmt->bindValue(':comp_name', $this->company_name, PDO::PARAM_STR);
            $stmt->bindValue(':cont_name', $this->contact_name, PDO::PARAM_STR);
            $stmt->bindValue(':title', $this->contact_title, PDO::PARAM_STR);
            $stmt->bindValue(':cont_phone', $this->contact_phone, PDO::PARAM_STR);
            $stmt->bindValue(':comp_phone', $this->company_phone, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->supplier_email, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->supplier_id, PDO::PARAM_INT);
            return $stmt->execute();
        }
        return false;
    }


    // Get array of suppliers
    public static function getAllSuppliers()
    {
        $db = static::getDB();
        $stmt = $db->query('SELECT * FROM suppliers');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Find supplier by id
    public static function getSupplierByID($id)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM suppliers WHERE supplier_id = :supplier_id');
        $stmt->execute([':supplier_id'=>$id]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Find supplier by contact name
    public static function getSupplierByContactName($contact_name)
    {
        $db = static::getDB();
        $stmt = $db->prepare("SELECT * FROM suppliers WHERE contact_name = :contact_name"); // contact_name = :contact_name WHERE contact_name LIKE '%:contact_name%'
        $stmt->execute([':contact_name'=>$contact_name]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Find id of supplier by company name 
    public static function getSupplierIDByCompanyName($companyName)
    {
        $db = static::getDB();
        $stmt = $db->prepare("SELECT (supplier_id) FROM suppliers WHERE company_name = :company_name"); // contact_name = :contact_name WHERE contact_name LIKE '%:contact_name%'
        $stmt->execute([':company_name'=>$companyName]); 
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Find compnay_name by ID
    public static function getSupplierCompanyNameByID($supplier_id)
    {
        $db = static::getDB();
        $stmt = $db->prepare("SELECT (company_name) FROM suppliers WHERE supplier_id = :supplier_id"); // contact_name = :contact_name WHERE contact_name LIKE '%:contact_name%'
        $stmt->execute([':supplier_id'=>$supplier_id]); 
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Find supplier by email
    public static function getSupplierByEmail($supplier_email)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM suppliers WHERE supplier_email = :supplier_email');
        $stmt->execute([':supplier_email'=>$supplier_email]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Delete supplier from db
    public static function deleteSupplier($id)
    {
        $db = static::getDB();
        $stmt = $db->prepare('DELETE FROM suppliers WHERE supplier_id = :supplier_id');
        return $stmt->execute([':supplier_id'=>$id]); 
    }

        // Generate new supplier ID
        public static function generateSupplierID()
        {
            $db = static::getDB();
            $stmt = $db->prepare("SELECT MAX(supplier_id) AS newID FROM suppliers");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result[0]['newID'] + 1;
        }




    // function for validate suppliers details
    public function validate()
    {
        // validate company name
        if (empty($this->company_name)) {
            $this->errors[] = "יש להזין שם חברה";
        }
        if (strlen($this->company_name) > 25) {
            $this->errors[] = "הזנת שם חברה ארוך מידי";
        }
        // validate contact name
        if (empty($this->contact_name)) {
            $this->errors[] = "יש להזין שם איש קשר";
        }
        if ($this->contact_name>25) {
            $this->errors[] = "הזנת שם איש קשר ארוך מידי";
        }
        // validate title name
        if (empty($this->contact_title)) {
            $this->errors[] = "יש להזין תפקיד";
        }
        if ($this->contact_name>25) {
            $this->errors[] = "הזנת תפקיד איש קשר ארוך מידי";
        }
        // contact number
        $this->contact_phone = str_replace('-','',$this->contact_phone);
        if (preg_match("/^[0]{1}[5]{1}[0-9]{8}$/", $this->contact_phone) == 0) {
            $this->errors[] = 'מספר פלאפון של איש קשר לא תקין';
        }
        // company number
        $this->company_phone = str_replace('-','',$this->company_phone);
        if (preg_match("/^[0]{1}[5]{1}[0-9]{8}$/", $this->company_phone) == 0) {
            $this->errors[] = 'מספר פלאפון של החברה לא תקין';
        }
        // email address
        if (filter_var($this->supplier_email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'אימייל ספק לא תקין';
        }
    }


    // Create suppliers table
    public static function createSuppliersTable($suppliers)
    { 
        $tableRows = "";
        foreach($suppliers as $s) 
        {
            $sid= $s['supplier_id'];
            $scomn = $s['company_name'];
            $scond= $s['contact_name'];
            $sct = $s['contact_title'];
            $sconp = $s['contact_phone'];
            $scomp = $s['company_phone'];
            $se = $s['supplier_email'];
          
            $tableRows .= "<tr>
            <th scope='row'>$sid</th>
            <td>$scomn</td>
            <td>$scond</td>
            <td>$sct</td>
            <td>$sconp</td>
            <td>$scomp</td>
            <td>$se</td>
            <!--Product row buttons-->
            <td>
                <div class='d-flex justify-content-end'>
                    <form action='/admin/suppliers/delete/$sid' method='POST'>
                        <button class='btn btn-primary mr-2' name='deleteProduct'>הסרה</button>
                    </form>
                    <a href='/admin/suppliers/edit/$sid' class='btn btn-primary mr-2'>עדכון</a>
                    <div class='d-flex align-items-end'>
                        <a href='#supplier_info$sid' class='btn btn-default' data-toggle='collapse'>
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
        </tr>";
        }
        return $tableRows;
    }
 

}
