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

    /**
     * Enriches appreciation with necessary data
     * for view
     * 
     * @param mixed $appreciation 
     * @return mixed 
     */
    static function enrichAppreciation($appreciation) {
        $timeAgo = new \Westsworld\TimeAgo();
        $appreciationTime = \DateTime::createFromFormat( 'U', $appreciation['time']);
        $appreciation['timeFormatted'] = $timeAgo->inWords($appreciationTime);
        $appreciation['count'] = Db::getLikes(intval($appreciation['id']));

        return $appreciation;
    }
}