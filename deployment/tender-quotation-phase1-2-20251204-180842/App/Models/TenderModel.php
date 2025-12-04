<?php

namespace App\Models;

use PDO;

/**
 * Tender model
 *
 * Enhanced with expiry management
 * @author Rakheoana Lefela
 * @updated Nhlanhla Mnyandu - 2025-12-04 (Added expiry management)
 */
class TenderModel extends \Core\Repository
{

    /**
     * Get all active tenders (backward compatibility)
     * @return array
     */
    public static function Get()
    {
        try {
            $db = static::getDB();
            $stmt = $db->query('SELECT * FROM tenders WHERE `isActive` = 1 ORDER BY `createdAt` DESC');
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Get only active (non-expired) tenders
     * Excludes status = 4 (expired/archived)
     * @return array
     */
    public static function GetActive()
    {
        try {
            $db = static::getDB();
            $stmt = $db->prepare('SELECT * FROM tenders
                                  WHERE `isActive` = 1
                                  AND `status` != 4
                                  ORDER BY `createdAt` DESC');
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;

        } catch (PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }

    /**
     * Get only archived (expired) tenders
     * Returns status = 4 (expired/archived)
     * @return array
     */
    public static function GetArchived()
    {
        try {
            $db = static::getDB();
            $stmt = $db->prepare('SELECT * FROM tenders
                                  WHERE `isActive` = 1
                                  AND `status` = 4
                                  ORDER BY `dueDate` DESC');
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;

        } catch (PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }

    /**
     * Automatically archive expired tenders
     * Sets status = 4 for tenders past their dueDate
     * @return int Number of tenders archived
     */
    public static function ArchiveExpired()
    {
        try {
            $db = static::getDB();
            $today = date('Y-m-d');

            // Update tenders where dueDate has passed and status is not already archived
            $stmt = $db->prepare('UPDATE tenders
                                  SET `status` = 4
                                  WHERE `isActive` = 1
                                  AND `status` != 4
                                  AND `dueDate` < :today');
            $stmt->bindParam(':today', $today, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->rowCount();

        } catch (PDOException $e) {
            echo $e->getMessage();
            return 0;
        }
    }

    /**
     * Get tender by ID (with prepared statement for security)
     * @param int $id
     * @return array|false
     */
    public static function getServiceById($id)
    {
        try {
            $db = static::getDB();
            $stmt = $db->prepare("SELECT * FROM tenders WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * Update tender (with prepared statement for security)
     * @param array $data
     * @return bool
     */
    public static function Update($data)
    {
        try {
            $db = static::getDB();
            $sql = "UPDATE tenders
                    SET `title` = :title,
                        `subtitle` = :subtitle,
                        `body` = :body,
                        `updatedAt` = :updatedAt,
                        `status` = :status
                    WHERE `id` = :id";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':title', $data['title'], PDO::PARAM_STR);
            $stmt->bindParam(':subtitle', $data['subtitle'], PDO::PARAM_STR);
            $stmt->bindParam(':body', $data['body'], PDO::PARAM_STR);
            $stmt->bindParam(':updatedAt', $data['updatedAt'], PDO::PARAM_STR);
            $stmt->bindParam(':status', $data['status'], PDO::PARAM_INT);
            $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * Save new tender (with prepared statement for security)
     * @param array $data
     * @return int|false Last insert ID or false on failure
     */
    public static function Save($data)
    {
        global $context;

        try {
            $db = static::getDB();
            $sql = "INSERT INTO tenders
                    (title, subtitle, body, isActive, createdAt, updatedBy, reference, location, dueDate, status)
                    VALUES
                    (:title, :subtitle, :body, :isActive, :createdAt, :updatedBy, :reference, :location, :duedate, :status)";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':title', $data['title'], PDO::PARAM_STR);
            $stmt->bindParam(':subtitle', $data['subtitle'], PDO::PARAM_STR);
            $stmt->bindParam(':body', $data['body'], PDO::PARAM_STR);
            $stmt->bindParam(':isActive', $data['isActive'], PDO::PARAM_INT);
            $stmt->bindParam(':createdAt', $data['createdAt'], PDO::PARAM_STR);
            $stmt->bindParam(':updatedBy', $data['updatedBy'], PDO::PARAM_STR);
            $stmt->bindParam(':reference', $data['reference'], PDO::PARAM_STR);
            $stmt->bindParam(':location', $data['location'], PDO::PARAM_STR);
            $stmt->bindParam(':duedate', $data['duedate'], PDO::PARAM_STR);
            $stmt->bindParam(':status', $data['status'], PDO::PARAM_INT);

            $stmt->execute();
            return $db->lastInsertId();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * Soft delete tender
     * @param int $id
     * @return bool
     */
    public static function Delete($id)
    {
        try {
            $db = static::getDB();
            $stmt = $db->prepare("UPDATE tenders SET `isActive` = 0 WHERE `id` = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * Restore soft-deleted tender
     * @param int $id
     * @return bool
     */
    public static function Restore($id)
    {
        try {
            $db = static::getDB();
            $stmt = $db->prepare("UPDATE tenders SET `isActive` = 1 WHERE `id` = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

}
