<?php

namespace App\Models;

use DatePeriod;
use PDO;

/**
 * Post model
 *
 * PHP version 5.4
 */
class AgendaModel extends \Core\Repository
{
    /**
     * Get all the posts as an associative array
     * @return array
     */

    public static function Get()
    {
        try {
            $db = static::getDB();
            $stmt = $db->query('SELECT * FROM agendas WHERE `isActive` = 1 order by `createdAt` DESC');
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    public static function GetById($id)
    {
        try {
            $db = static::getDB();
            $stmt = $db->prepare("SELECT * FROM agendas WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


    public static function GetByDate(
        ?DatePeriod $oDatePeriod = null
    )
    {


        try {
            $db = static::getDB();
            $start = $oDatePeriod->getStartDate()->format('Y-m-d');
            $end = $oDatePeriod->getEndDate()->format('Y-m-d');

            $sql = 'SELECT * FROM agendas WHERE `isActive` = 1
                    AND createdAt BETWEEN :dateStart AND :dateEnd 
                    ORDER BY `createdAt` ASC';

            $stmt = $db->prepare($sql); 
            $stmt->bindParam(':dateStart', $start);
            $stmt->bindParam(':dateEnd', $end);                
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    public static function Save($data)
    {
        $db = static::getDB();
        $sql = "INSERT INTO agendas (title, subtitle, body, createdAt, isActive, location, img_file, updatedBy)
                VALUES (:title, :subtitle, :body, now(), 1, :location, :img_file, :updatedBy)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':subtitle', $data['subtitle']);
        $stmt->bindParam(':body', $data['body']);
        $stmt->bindParam(':location', $data['location']);
        $stmt->bindParam(':img_file', $data['img_file']);
        $stmt->bindParam(':updatedBy', $data['updatedBy']);

        if ($stmt->execute()) {
            return $db->lastInsertId();
        }
        return false;
    }
    
    public static function Update($data)
    {
        $db = static::getDB();

        $sql = "UPDATE agendas SET
        `title` = :title,
        `subtitle` = :subtitle,
        `body` = :body,
        `updatedAt` = :updatedAt
        WHERE `id` = :id";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':subtitle', $data['subtitle']);
        $stmt->bindParam(':body', $data['body']);
        $stmt->bindParam(':updatedAt', $data['updatedAt']);
        $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }


    public static function Delete($id)
    {
        $db = static::getDB();
        $sql = "UPDATE agendas SET `isActive` = 0 WHERE `id` = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function Restore($id)
    {
        $db = static::getDB();
        $sql = "UPDATE agendas SET `isActive` = 1 WHERE `id` = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

}
