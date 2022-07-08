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
            $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
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
        return $statement->fetchAll();
    }

    /**
     * Get latest message number
     * @return int 
     * @throws PDOException 
     */
    static function getLastMessageId() {
        $lastMessageIdStatement = self::$db->prepare("SELECT id FROM appreciations ORDER by id DESC LIMIT 1");
        $lastMessageIdStatement->execute();
        return intval($lastMessageIdStatement->fetch()['id']);
    }

    /**
     * Get latest "published" appreciation
     * 
     * @return mixed 
     */
    static function getLatestAppreciation() {
        $latestId = intval(self::getMetadata('latestAppreciation', 0));
        $appreciationStatement = self::$db->prepare("SELECT * FROM appreciations WHERE id = ?");
        $appreciationStatement->execute([$latestId]);
        return $appreciationStatement->fetch();
    }

    /**
     * Will increment the latest appreciation id at most every 24 hours
     * 
     * @return void 
     */
    static function maybeIncrementLatestAppreciationId() {
        $lastUpdate = intval(self::getMetadata('lastUpdate'));
        $currentTime = time();
    
        if(($currentTime - $lastUpdate) > 86400) {
    
            $lastMessageId = self::getLastMessageId();
            $currentCount = intval(self::getMetadata('latestAppreciation', 0));
    
            if($currentCount < $lastMessageId) {
                $incrementedCount = $currentCount + 1;
    
                self::setMetadata('latestAppreciation', $incrementedCount);
                self::setMetadata('lastUpdate', $currentTime);
            }
        }
    }
}