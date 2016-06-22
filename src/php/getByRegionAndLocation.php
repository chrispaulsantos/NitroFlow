Å<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 6/16/16
 * Time: 5:12 PM
 */

    require_once "database_connect.php";
    require "location_object.php";

    $region = $_GET["region"];
    $location_id = $_GET["location_id"];

    try {
        $stmt = DBConnection::instance()->prepare("CALL `getByRegionAndLocation`(:location_id,:region)");
        $stmt->bindParam(":location_id", $location_id);
        $stmt->bindParam(":region", $region);
        $stmt->execute();
    } catch (Exception $e){
        error_log("Error: " .$e->getMessage());
    }