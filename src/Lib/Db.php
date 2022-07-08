<?php
namespace Khromov\AppreciationJar\Lib;

class Db {
    private static $db = null;

    static function initialize() {
        return self::get();
    }

    static function get() {
        if(self::$db !== null) {
            return self::$db;
        }

        $fileName = __DIR__ . "/../../db/appreciations.sqlite";
        $dsn = "sqlite:$fileName";
    
        try {
            // Refactor to use class state
            $db = new \PDO($dsn);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$db = $db;
            return $db;
        } catch (\PDOException $e) {
            echo "Failed to connect to the database using DSN:<br>$dsn<br>";
            throw $e;
        }

        return null;
    }

    /**
     * Get metadata
     * 
     * @param string $key 
     * @param mixed $defaultValue 
     * @return mixed 
     * @throws PDOException 
     */
    static function getMetadata($key, $defaultValue = null) {
        $statement = self::$db->prepare("SELECT * FROM metadata WHERE key = ? LIMIT 1");
        $statement->execute([$key]);
        return $statement->fetch()['value'] ?? $defaultValue;
    }

    /**
     * Set metadata
     * 
     * @param mixed $key 
     * @param mixed $value 
     * @return array|false 
     * @throws PDOException 
     */
    static function setMetadata($key, $value) {
        $statement = self::$db->prepare("INSERT INTO metadata(key,value) VALUES(?,?) ON CONFLICT(key) DO UPDATE SET value = ?;");
        $statement->execute([$key, $value, $value]);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get latest message number
     * @return int 
     * @throws PDOException 
     */
    static function getLastMessageId() {
        $lastMessageIdStatement = self::$db->prepare("SELECT id FROM appreciations ORDER by id DESC");
        $lastMessageIdStatement->execute();
        return intval($lastMessageIdStatement->fetch()['id']);
    }
}