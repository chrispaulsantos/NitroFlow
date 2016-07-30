<?php

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