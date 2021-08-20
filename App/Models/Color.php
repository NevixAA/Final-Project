<?php

namespace App\Models;

use PDO;

class Color extends \Core\Model
{

    // Get color name by color id
    public static function getColorDisplayNameByID($colorID)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT display_name AS color_d_name FROM colors WHERE color_id = :colorID');
        $stmt->execute([':colorID'=>$colorID]); 
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}

?>