<?php

namespace App\Models;

use PDO;

class Other extends \Core\Model
{
    public $errors = [];

    // Class constructor
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }


    // Get all settings 
    public static function getAllSettings()
    {
        $db = static::getDB();
        $stmt = $db->query('SELECT * FROM others');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Get setting value by id
    public static function getValueByID($ID)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT (string_value) FROM others WHERE id = :ID');
        $stmt->execute([':ID'=>$ID]); 
        return $stmt->fetch(PDO::FETCH_OBJ);
    }


    // Update setting value by ID
    public static function updateSettingValue($ID,$stringValue)
    {
        $db = static::getDB();
        $stmt = $db->prepare("UPDATE others SET string_value = :string_value WHERE id = :id");
        return $stmt->execute([':id'=>$ID ,':string_value'=> $stringValue]); 
    }


    // Save new settings
    public function save()
    {
        $this->validate();
        if (empty($this->errors)) {
            $db = static::getDB();
            $stmt = $db->prepare("INSERT INTO others (string_value, display_name) VALUES (:string_value, :display_name)");
            $result = $stmt->execute([
                ':string_value'  => $this->string_value,
                ':display_name'  => $this->display_name
            ]);
            if ($result) {
                    return true;
            }
        }
        return false;
    }

    
    // Function for validate setting details
    public function validate()
    {
        if (empty($this->string_value)) {
            $this->errors[] = "יש להזין ערך הגדרה";
        }
        if (strlen($this->string_value) > 255) {
            $this->errors[] = "הזנת ערך הגדרה ארוך מידי";
        }
        if (empty($this->display_name)) {
            $this->errors[] = "יש להזין שם תצוגה";
        }
        if (strlen($this->display_name) > 255) {
            $this->errors[] = "הזנת שם תצוגה ארוך מידי";
        }
    }
}
?>