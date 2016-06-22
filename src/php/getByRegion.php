<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 6/21/16
 * Time: 5:26 PM
 */

    require_once "database_connect.php";
    require "location_object.php";

    $region = $_GET["region"];

    try {
        $stmt = DBConnection::instance()->prepare("CALL `getByRegion`(:region)");
        $stmt->bindParam(":region", $region);
        $stmt->execute();
    } catch (Exception $e){
        error_log("Error: " .$e->getMessage());
    }