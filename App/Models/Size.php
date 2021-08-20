<?php

namespace App\Models;

use PDO;

class Size extends \Core\Model
{
    // Get size name by size id
    public static function getSizeDisplayNameByID($sizeID)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT display_name AS size_d_name FROM sizes WHERE size_id = :sizeID');
        $stmt->execute([':sizeID'=>$sizeID]); 
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}

?>