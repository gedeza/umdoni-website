<?php

namespace App\Models;
use PDO;

/**
 * Logs Model - Handles activity logging and error tracking
 *
 * PHP version 5.4
 */
class LogsModel extends \Core\Repository
{
    /**
     * Get all logs with optional filtering
     *
     * @param string|null $type Filter by log type (login, logout, error, warning, info)
     * @param int|null $limit Limit number of results
     * @return array
     */
    public static function Get($type = null, $limit = null)
    {
        try {
            $db = static::getDB();

            $sql = 'SELECT * FROM logs';

            if ($type !== null) {
                $sql .= ' WHERE status = :type';
            }

            $sql .= ' ORDER BY time_log DESC';

            if ($limit !== null) {
                $sql .= ' LIMIT :limit';
            }

            $stmt = $db->prepare($sql);

            if ($type !== null) {
                $stmt->bindParam(':type', $type, PDO::PARAM_STR);
            }

            if ($limit !== null) {
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            }

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results;
        } catch (PDOException $e) {
            error_log("LogsModel::Get() Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get logs by type
     *
     * @param string $type Log type (login, logout, error, warning, info)
     * @return array
     */
    public static function GetByType($type)
    {
        return self::Get($type);
    }

    /**
     * Get recent logs with optional type filter
     *
     * @param int $limit Number of logs to retrieve
     * @param string|null $type Optional log type filter
     * @return array
     */
    public static function GetRecent($limit = 100, $type = null)
    {
        return self::Get($type, $limit);
    }

    /**
     * Get log by ID
     *
     * @param int $id Log ID
     * @return array|null
     */
    public static function getById($id)
    {
        try {
            $db = static::getDB();
            $stmt = $db->prepare("SELECT * FROM logs WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $e) {
            error_log("LogsModel::getById() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Save user login activity
     *
     * @param array $data User login data
     * @return int|false Log ID or false on failure
     */
    public static function Save($data)
    {
        try {
            $db = static::getDB();
            $date = date("Y-m-d H:i:s");

            $sql = "INSERT INTO logs (`userId`, `username`, `email`, `time_log`, `status`, `last_login`, `location`)
                    VALUES (:userId, :username, :email, :time_log, :status, :last_login, :location)";

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':userId', $data['user_id'], PDO::PARAM_STR);
            $stmt->bindParam(':username', $data['username'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
            $stmt->bindParam(':time_log', $date, PDO::PARAM_STR);
            $stmt->bindParam(':status', $data['status'], PDO::PARAM_STR);
            $stmt->bindParam(':last_login', $date, PDO::PARAM_STR);

            // Get IP address for location tracking
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
            $stmt->bindParam(':location', $ipAddress, PDO::PARAM_STR);

            $stmt->execute();
            return $db->lastInsertId();
        } catch (PDOException $e) {
            error_log("LogsModel::Save() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Log an error or event to the database
     *
     * @param string $message Error message or event description
     * @param string $level Log level (error, warning, info)
     * @param array $context Additional context (userId, username, email, etc.)
     * @return int|false Log ID or false on failure
     */
    public static function LogError($message, $level = 'error', $context = [])
    {
        try {
            $db = static::getDB();
            $date = date("Y-m-d H:i:s");

            // Get user info from context or session
            $userId = $context['userId'] ?? ($_SESSION['profile'][0]['user_id'] ?? 'system');
            $username = $context['username'] ?? ($_SESSION['profile'][0]['username'] ?? 'System');
            $email = $context['email'] ?? ($_SESSION['profile'][0]['email'] ?? '');

            // Get IP address and user agent
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

            // Combine IP and user agent for location field
            $location = $ipAddress . ' | ' . substr($userAgent, 0, 100);

            $sql = "INSERT INTO logs (`userId`, `username`, `email`, `time_log`, `status`, `actions`, `location`)
                    VALUES (:userId, :username, :email, :time_log, :status, :actions, :location)";

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':time_log', $date, PDO::PARAM_STR);
            $stmt->bindParam(':status', $level, PDO::PARAM_STR);
            $stmt->bindParam(':actions', $message, PDO::PARAM_STR);
            $stmt->bindParam(':location', $location, PDO::PARAM_STR);

            $stmt->execute();
            return $db->lastInsertId();
        } catch (PDOException $e) {
            error_log("LogsModel::LogError() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a log entry
     *
     * @param int $id Log ID
     * @return bool
     */
    public static function Delete($id)
    {
        try {
            $db = static::getDB();
            $sql = "DELETE FROM logs WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("LogsModel::Delete() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update logout timestamp for user session
     *
     * @param array $data User logout data
     * @return bool
     */
    public static function UserLogout($data)
    {
        try {
            $db = static::getDB();
            $date = date("Y-m-d H:i:s");

            $sql = "UPDATE logs SET `logout` = :logout
                    WHERE `userId` = :userId AND `id` = :id";

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':logout', $date, PDO::PARAM_STR);
            $stmt->bindParam(':userId', $data['user_id'], PDO::PARAM_STR);
            $stmt->bindParam(':id', $data['log_id'], PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("LogsModel::UserLogout() Error: " . $e->getMessage());
            return false;
        }
    }
}
