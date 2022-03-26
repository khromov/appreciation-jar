<?php
namespace Khromov\AppreciationJar\Lib;

class Db {
    static function initDb() {
        $fileName = __DIR__ . "/../../db/appreciations.sqlite";
        $dsn = "sqlite:$fileName";
    
        try {
            $db = new \PDO($dsn);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $db;
        } catch (\PDOException $e) {
            echo "Failed to connect to the database using DSN:<br>$dsn<br>";
            throw $e;
        }

        return null;
    }
}