<?php

class Database
{
    private static $dao = null;

    private function __construct() {
    }

    public static function getDao() {
        if(is_null(self::$dao)) {
            self::$dao = new PDO("mysql:host=localhost;dbname=database", 'user', 'password');
        }
        return self::$dao;
    }
}
?>