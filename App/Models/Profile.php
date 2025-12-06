<?php

namespace App\Models;
use PDO;
use App\Models\LogsModel;
use Exception;
/**
 * Post model
 *
 * PHP version 5.4
 */
class Profile extends \Core\Repository
{
    /**
     * Get all the posts as an associative array
     * @return array
     */
    
     public static function getAll()
    {
        try {
            $db = static::getDB();
            $stmt = $db->query("SELECT * FROM users 
                                LEFT JOIN profile ON (users.user_id = profile.user_id) 
                                ORDER BY users.createdAt DESC");                                  
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


    public static function getUser($data)
    {
        try {
            $db = static::getDB();
            $stmt = $db->query("SELECT * FROM users 
                                LEFT JOIN profile ON (users.user_id = profile.user_id)
                                WHERE users.email LIKE '%$data%' 
                                ORDER BY users.createdAt DESC");                                  
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


    public static function getById($user_id)
    {
            try {
                $db = static::getDB();
                $stmt = $db->query("SELECT * FROM users
                                    LEFT JOIN profile ON (users.user_id = profile.user_id)
                                    WHERE users.user_id = $user_id");
                   $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                   return $results;                   
            } catch (PDOException $e) {
                echo $e->getMessage();
            } 
    }

    
    public static function Save($data)
    {
        $db = static::getDB(); 
        $sql = "INSERT into profile ( name, email, message, created_date, status) 
                VALUES ( '$data[name]', '$data[email]','$data[message]' , now() , 1)";
        $stmt = $db->exec($sql);
        return $stmt;
    }



    public static function Update()
    {
        try {
            $db = static::getDB();
            $stmt = $db->query('SELECT id, title, content FROM posts ORDER BY created_at');
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;   
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


    public static function Delete()
    {
        try {
            $db = static::getDB();
            $stmt = $db->query('SELECT id, title, content FROM posts ORDER BY created_at');
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function Login($data)
    {
        global $context;
        $exists = self::getUser($data['username']);
        $context = (object) array_merge( (array)$context, array( 'profile' => $exists ) );
        // $context->profile = $exists;
        // we can determine the role from here and use it in the future
        // compare passwo
        

        // get the role
        if(!empty($exists)) 
        {
            try {
                 self::authenticate($exists, $data);
            } catch (\Throwable $th) {
                throw $th;
            }
            return true;
        }
        else
        {
            throw new Exception("Error Processing Request", 1);
        }
    }


     static function Authenticate($profile, $aData)
    {
        global $context;

        try {
            // 1. Check if account is locked FIRST
            if ($profile[0]["locked"] == '1') {
                $context->isLoggedIn = false;
                throw new Exception("Account is locked. Please contact administrator.");
            }

            // 2. Check if access_token exists (OAuth/Cognito user)
            if (!empty($profile[0]['access_token'])) {
                $context->isLoggedIn = false;
                throw new Exception("Please use OAuth authentication for this account.");
            }

            // 3. CRITICAL: Validate password BEFORE allowing access
            // Support both hashed (new) and plaintext (legacy) passwords
            $passwordValid = false;

            if (password_get_info($profile[0]['password'])['algo'] !== null) {
                // Password is hashed - use password_verify()
                $passwordValid = password_verify($aData['password'], $profile[0]['password']);
            } else {
                // Legacy plaintext password - direct comparison (temporary backward compatibility)
                $passwordValid = ($aData['password'] === $profile[0]['password']);

                // IMPORTANT: Re-hash legacy password on successful login
                if ($passwordValid) {
                    $hashedPassword = password_hash($aData['password'], PASSWORD_DEFAULT);
                    $db = static::getDB();
                    $sql = "UPDATE users SET `password` = :password WHERE `user_id` = :user_id";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':password', $hashedPassword);
                    $stmt->bindParam(':user_id', $profile[0]['user_id'], PDO::PARAM_INT);
                    $stmt->execute();
                }
            }

            if (!$passwordValid) {
                $context->isLoggedIn = false;

                // Log failed login attempt to database
                LogsModel::LogError(
                    'Failed login attempt for user: ' . $profile[0]['username'],
                    'error',
                    [
                        'userId' => $profile[0]['user_id'] ?? 'unknown',
                        'username' => $profile[0]['username'] ?? 'unknown',
                        'email' => $profile[0]['email'] ?? 'unknown'
                    ]
                );

                throw new Exception("Invalid username or password.");
            }

            // 4. Password is correct - proceed with login
            $context->isLoggedIn = true;

            // 5. Create activity log entry
            $logId = LogsModel::Save($profile[0]);
            $profile[0]['log_id'] = $logId;

            // 6. Set session and cookie
            $_SESSION['profile'] = $profile[0];
            setcookie("auth", md5($profile[0]['password']), time() + 3600 * 30, '/');

            return true;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}