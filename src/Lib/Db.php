<?php
namespace Khromov\AppreciationJar\Lib;

class Db {
    /**
     * @var \PDO | null
     */
    private static $db = null;

    static function initialize() {
        $db = self::get();

        // https://tableplus.com/blog/2018/04/sqlite-check-whether-a-table-exists.html
        $tableExistsStatement = $db->prepare("SELECT * FROM sqlite_master WHERE type='table' AND name = ?;");
        $tableExistsStatement->execute(['appreciations']);
        $tableExistsResult = $tableExistsStatement->fetchAll();

        // No table exists, they need to be created
        if(sizeof($tableExistsResult) === 0) {
            $createAppreciationsStatement = $db->prepare('
                CREATE TABLE IF NOT EXISTS "appreciations" (
                    "id"    INTEGER NOT NULL,
                    "time"  INTEGER,
                    "text"  TEXT,
                    "author"    TEXT,
                    "metadata"  TEXT,
                    PRIMARY KEY("id" AUTOINCREMENT)
                );
            ');

            $createMetadataStatement = $db->prepare('
                CREATE TABLE IF NOT EXISTS "metadata" (
                    "key"   TEXT UNIQUE,
                    "value" TEXT,
                    PRIMARY KEY("key")
                );
            ');

            $createAppreciationsStatement->execute();
            $createMetadataStatement->execute();

            self::setMetadata('dbVersion', 1);
        }

        // Any additional migrations would go here

        return $db;
    }

    static function createSchema() {
        /*

        */
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
     * Get latest "published" appreciation
     * 
     * @return mixed 
     */
    static function getVisibleAppreciations() {
        $latestId = intval(self::getMetadata('latestAppreciation', 0));
        $appreciationStatement = self::$db->prepare("SELECT * FROM appreciations WHERE id <= ? ORDER by time DESC");
        $appreciationStatement->execute([$latestId]);
        return $appreciationStatement->fetchAll();
    }

    /**
     * Will increment the latest appreciation id at most every 24 hours
     * 
     * @return void 
     */
    static function maybeIncrementLatestAppreciationId($overrideTimer = false) {
        $lastUpdate = intval(self::getMetadata('lastUpdate'));
        $currentTime = time();
        $config = Helpers::getConfig();

        if($overrideTimer || $lastUpdate === 0 || ($currentTime - $lastUpdate) > intval($config['new_appreciation_every_x_seconds'])) {
    

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