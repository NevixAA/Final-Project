<?php

namespace App\Models;

use PDO;
use DateTime;
use \App\Token;

/**
 * User model
 */
class User extends \Core\Model
{

    public $errors = [];

    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    /**
     * Save the user model with the current property values
     *
     * @return boolean  True if the user was saved, false otherwise
     */
    public function save()
    {
        $this->validate();
        $id=$this->getNextId()->id+1;
        if (empty($this->errors)) {
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
            $sql = 'INSERT INTO users (id,name, last_name, email, password_hash, phone_num, house_num, street_name, city, date_of_birth)
                    VALUES (:id, :name, :last_name, :email, :password_hash, :phone, :house, :street, :city, :date)';
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':last_name', $this->last_name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':phone', $this->phone_num, PDO::PARAM_STR);
            $stmt->bindValue(':house', $this->house_num, PDO::PARAM_STR);
            $stmt->bindValue(':street', $this->street_name, PDO::PARAM_STR);
            $stmt->bindValue(':city', $this->city, PDO::PARAM_STR);
            $stmt->bindValue(':date', $this->date_of_birth, PDO::PARAM_STR);
            return $stmt->execute();
        }
        return false;
    }

    
    // Edit specific user
    public function edit()
    {
        $this->validateEdit();
        if (empty($this->errors)) {
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
            $db = static::getDB();
            $stmt = $db->prepare('UPDATE users SET name=:name,last_name=:last_name,email=:email,password_hash=:password_hash,phone_num=:phone,house_num=:house,street_name=:street,city=:city,date_of_birth=:date WHERE id = :id');
            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':last_name', $this->last_name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':phone', $this->phone_num, PDO::PARAM_STR);
            $stmt->bindValue(':house', $this->house_num, PDO::PARAM_STR);
            $stmt->bindValue(':street', $this->street_name, PDO::PARAM_STR);
            $stmt->bindValue(':city', $this->city, PDO::PARAM_STR);
            $stmt->bindValue(':date', $this->date_of_birth, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            return $stmt->execute();
        }
        return false;
    }



    // Validate current property values, adding valiation error messages to the errors array property
    public function validate()
    {
        // Name
        if (empty($this->name)) {
            $this->errors[] = 'יש להזין שם פרטי';
        }

        // Last Name
        if (empty($this->last_name)) {
            $this->errors[] = 'יש להזין שם משפחה';
        }

        // email address
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'אימייל לא תקין';
        }
        if (static::emailExists($this->email)) {
            $this->errors[] = 'האימייל כבר תפוס';
        }

        // Password
        if (strlen($this->password) < 6) {
            $this->errors[] = 'עלייך להכניס סיסמא בעלת 6 ספרות או יותר';
        }
        if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
            $this->errors[] = 'סיסמא חייבת להכיל לפחות אות אחתה';
        }
        if (preg_match('/.*\d+.*/i', $this->password) == 0) {
            $this->errors[] = 'הסיסמא חייבת להכיל פחות מספר אחד';
        }

        // Cellphone number
        $this->phone_num = str_replace('-','',$this->phone_num);
        if (preg_match("/^[0]{1}[5]{1}[0-9]{8}$/", $this->phone_num) == 0) {
            $this->errors[] = 'מספר פלאפון לא תקין';
        }

        // House number
        if(preg_match("/^[1-9]{1,3}$/", $this->house_num) == 0) {
            $this->errors[] = 'מספר בית לא תקין';
        }

        // Street name
        if (empty($this->street_name)) {
            $this->errors[] = 'יש להזין שם הרחוב';
        }
        $street = str_replace(' ','',$this->street_name); 
        
        if (preg_match('/^\p{Hebrew}+$/u', $street) == 0) {
            $this->errors[] = 'שם הרחוב חייב להכיל אותיות בלבד';
        }

        if (empty($this->city)) {
            $this->errors[] = 'יש להזין שם עיר';
        }
        
        if (preg_match('/^\p{Hebrew}+$/u', $this->city) == 0) {
            $this->errors[] = 'שם העיר חייב להכיל אותיות בלבד';
        }

        // Date of birth
        if (empty($this->date_of_birth)) {
            $this->errors[] = 'יש להזין תאריך לידה';
        }else {
            $date_now = new DateTime();
            $dateOfBirth   = new DateTime($this->date_of_birth);
            if ($date_now < $dateOfBirth)
                $this->errors[] = 'התאריך שהזנת לא יכול להיות גדול מהתאריך הנוכחי';
        }
    }

    

    // Validate current property values, adding valiation error messages to the errors array property
    public function validateEdit()
    {
        // Name
        if (empty($this->name)) {
            $this->errors[] = 'יש להזין שם פרטי';
        }

        // Last Name
        if (empty($this->last_name)) {
            $this->errors[] = 'יש להזין שם משפחה';
        }

        // email address
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'אימייל לא תקין';
        }
        
        // Password
        if (strlen($this->password) < 6) {
            $this->errors[] = 'עלייך להכניס סיסמא בעלת 6 ספרות או יותר';
        }
        if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
            $this->errors[] = 'סיסמא חייבת להכיל לפחות אות אחתה';
        }
        if (preg_match('/.*\d+.*/i', $this->password) == 0) {
            $this->errors[] = 'הסיסמא חייבת להכיל פחות מספר אחד';
        }

        // Cellphone number
        $this->phone_num = str_replace('-','',$this->phone_num);
        if (preg_match("/^[0]{1}[5]{1}[0-9]{8}$/", $this->phone_num) == 0) {
            $this->errors[] = 'מספר פלאפון לא תקין';
        }

        // House number
        if(preg_match("/^[1-9]{1,3}$/", $this->house_num) == 0) {
            $this->errors[] = 'מספר בית לא תקין';
        }

        // Street name
        if (empty($this->street_name)) {
            $this->errors[] = 'יש להזין שם הרחוב';
        }
        $street = str_replace(' ','',$this->street_name); 
        
        if (preg_match('/^\p{Hebrew}+$/u', $street) == 0) {
            $this->errors[] = 'שם הרחוב חייב להכיל אותיות בלבד';
        }

        if (empty($this->city)) {
            $this->errors[] = 'יש להזין שם עיר';
        }
        
        if (preg_match('/^\p{Hebrew}+$/u', $this->city) == 0) {
            $this->errors[] = 'שם העיר חייב להכיל אותיות בלבד';
        }

        // Date of birth
        if (empty($this->date_of_birth)) {
            $this->errors[] = 'יש להזין תאריך לידה';
        }else {
            $date_now = new DateTime();
            $dateOfBirth   = new DateTime($this->date_of_birth);
            if ($date_now < $dateOfBirth)
                $this->errors[] = 'התאריך שהזנת לא יכול להיות גדול מהתאריך הנוכחי';
        }
    }


    /**
     * See if a user record already exists with the specified email
     *
     * @param string $email email address to search for
     *
     * @return boolean  True if a record already exists with the specified email, false otherwise
     */
    public static function emailExists($email)
    {
        return static::findByEmail($email) !== false;
    }


    /**
     * Find a user model by email address
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }


    /**
     * Authenticate a user by email and password.
     *
     * @param string $email email address
     * @param string $password password
     *
     * @return mixed  The user object or false if authentication fails
     */
    public static function authenticate($email, $password)
    {
        $user = static::findByEmail($email);

        if ($user) {
            if (password_verify($password, $user->password_hash)) {
                return $user;
            }
        }

        return false;
    }


    /**
     * Find a user model by ID
     *
     * @param string $id The user ID
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }


    /**
     * Remember the login by inserting a new unique token into the remembered_logins table
     * for this user record
     *
     * @return boolean  True if the login was remembered successfully, false otherwise
     */
    public function rememberLogin()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();
        $this->expiry_timestamp = time() + 60 * 60 * 24 * 30;  // 30 days from now
        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                VALUES (:token_hash, :user_id, :expires_at)';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);

        return $stmt->execute();
    }

    
    // Get all users
    public static function getAllUsers()
    {
        $db = static::getDB();
        $stmt = $db->query('SELECT * FROM users');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete specific user by user id
    public static function deleteUser($id)
    {
        $db = static::getDB();
        $stmt = $db->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute([':id'=>$id]); 
    }

    // Find email by user id
    public static function findEmailByID($id)
    {
        $sql = 'SELECT (email) FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public static function getNextId()
    {
        $db = static::getDB();
        $stmt = $db->query('SELECT MAX(id) as id FROM users');
        return $stmt->fetch(PDO::FETCH_OBJ);
    }


    
    
}
