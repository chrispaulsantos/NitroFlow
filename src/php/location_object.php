<?php
    require_once "database_connect.php";

    class location {

        public $id;
        public $location;
        public $current_capacity;

        public function insertLocation() {
            $stmt = DBConnection::instance()->prepare("INSERT INTO Locations('location') VALUES(':loc')");
            $stmt->bindParam(":loc", $location);
            $stmt->execute();
        }

        public function getLocationData() {
            $stmt = DBConnection::instance()->prepare("SELECT DISTINCT P_Id FROM Location_Data ORDER BY time_stamp DESC LIMIT 1");
            $stmt->execute();
        }
    }