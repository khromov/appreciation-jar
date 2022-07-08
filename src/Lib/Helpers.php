<?php
namespace Khromov\AppreciationJar\Lib;

use PDOException;

class Helpers {
    /**
     * Get the config
     * 
     * @return array 
     */
    static function getConfig() {
        $config = require '../config.php';
        return $config;
    }
}