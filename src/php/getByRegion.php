<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 6/16/16
 * Time: 5:12 PM
 */

    require_once "database_connect.php";
    require "location_object.php";

    $region = $_GET["region"];

    try {
        $stmt = DBConnection::instance()->prepare("SELECT * FROM Locations WHERE region = :region");
        $stmt->bindParam(":region", $region);
    }