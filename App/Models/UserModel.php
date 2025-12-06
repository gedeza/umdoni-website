<?php

namespace App\Models;

use PDO;

/**
 * Post model
 *
 * PHP version 5.4
 */
class UserModel extends \Core\Repository
{

    /**
     * Get all the posts as an associative array
     *
     * @return array
     */
    public static function getAll()
    {
  
        try {
            $db = static::getDB();
            $stmt = $db->query('SELECT * FROM users ORDER BY createdAt');
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


    
    public static function getUser($id)
    {
        try {
            $db = static::getDB();
            $stmt = $db->prepare("SELECT * FROM users
                                LEFT JOIN profile ON (users.user_id = profile.user_id)
                                WHERE users.user_id = :id
                                ORDER BY users.createdAt DESC");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function UpdateUser($data)
    {
        $db = static::getDB();

        // Update profile table
        $sql = "UPDATE profile SET `first_name` = :first_name, `last_name` = :last_name,
                `mobile_number` = :mobile_number, `address_1` = :address_1,
                `address_2` = :address_2, `postal_code` = :postal_code,
                `town` = :town, `city` = :city
                WHERE `user_id` = :user_id";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':mobile_number', $data['mobile_number']);
        $stmt->bindParam(':address_1', $data['address_1']);
        $stmt->bindParam(':address_2', $data['address_2']);
        $stmt->bindParam(':postal_code', $data['postal_code']);
        $stmt->bindParam(':town', $data['town']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        // Update users table
        $sql = "UPDATE users SET `email` = :email WHERE `user_id` = :user_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->execute();

       return $stmt;
    }


    public static function UpdateImage($data)
    {
        $db = static::getDB();

        $sql = "UPDATE profile SET `img_file` = :img_file, `location` = :location WHERE `user_id` = :user_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':img_file', $data['img_file']);
        $stmt->bindParam(':location', $data['location']);
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->execute();

       return $stmt;
    }

    public static function VerifyeUser($data)
    {
        $db = static::getDB();
        $sql = "UPDATE users SET `verified` = 1 WHERE `email` = :email";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $data['username']);
        $stmt->execute();

       return $stmt;
    }

    public static function Save($data)
    {
        global $context;
        $db = static::getDB();

        // Check if email already exists
        $checkSql = "SELECT user_id FROM users WHERE email = :email LIMIT 1";
        $checkStmt = $db->prepare($checkSql);
        $checkStmt->bindParam(':email', $data['email']);
        $checkStmt->execute();

        if ($checkStmt->fetch()) {
            throw new \Exception("Email address already exists");
        }

        // Hash password if provided (for admin-created users)
        // For Cognito users, password is already set
        if (isset($data['password']) && !empty($data['password'])) {
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            $hashedPassword = $data['password'] ?? null;
        }

        $sql = "INSERT into users (username,surname,email,password,status,locked, role_id, createdAt)
                VALUES (:username,:surname,:email,:password,:status,:locked, :role_id,:createdAt)";

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':surname', $data['surname']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':locked', $data['locked']);
        $stmt->bindParam(':role_id', $data['role_id']);
        $stmt->bindParam(':createdAt', $data['createdAt']);

        $stmt->execute();
        $user_id = $db->lastInsertId();

        if(isset($user_id) && (is_numeric($user_id)))
        {
            // Use prepared statement for profile insert
            $sql = "INSERT into profile (user_id, first_name, last_name)
                    VALUES (:user_id, :first_name, :last_name)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':first_name', $data['username']);
            $stmt->bindParam(':last_name', $data['surname']);
            $stmt->execute();

            if(!is_null($stmt))
            {
                $_SESSION['success'] = ['message' => 'User created successfully'];
            }
        }

       return $stmt;
    }




    public static function Delete($id)
    {
        $db = static::getDB();

        // Delete from profile first (foreign key dependency)
        $sql = "DELETE FROM profile WHERE `user_id` = :user_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Delete from users
        $sql = "DELETE FROM users WHERE `user_id` = :user_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $id, PDO::PARAM_INT);
        $stmt->execute();

       return $stmt;
    }


    public static function ChangeStatus($data)
    {
        $db = static::getDB();
        $sql = "UPDATE users SET `locked` = :locked WHERE `user_id` = :user_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':locked', $data['locked'], PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->execute();

       return $stmt;
    }

    public static function ChangeRole($data)
    {
        $db = static::getDB();
        $sql = "UPDATE users SET `role_id` = :role_id WHERE `user_id` = :user_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':role_id', $data['role_id'], PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->execute();

       return $stmt;
    }
}
