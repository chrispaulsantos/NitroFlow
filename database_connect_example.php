<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 8/2/16
 * Time: 8:34 AM
 */
    class DBConnection
    {
        public static $conn;

        public static function instance()
        {
            // Create conn (if null)
            try {
                DBConnection::$conn = DBConnection::$conn == null
                    ? new PDO(
                        "mysql:host=localhost;dbname=flow_data;charset=utf8",
                        'WebAccess', 'wyJE5N4h7BFNFane')
                    : DBConnection::$conn;
            } catch (Exception $e) {
                error_log("Cannot connect: " . $e->getMessage());
            }

            return DBConnection::$conn;
        }
    }