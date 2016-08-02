<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 8/2/16
 * Time: 8:55 AM
 */
error_log("Am I in?");
    require_once "database_connect.php";

    try {
        $stmt = DBConnection::instance()->prepare("SELECT DISTINCT `location` FROM `Locations`");
        $stmt->execute();
    } catch (Exception $e){
        error_log("Error: ") . $e->getMessage();
    }


    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $rows[] = $row;
    }


    createJSON($rows);

    function createJSON($rows){
        $json[] = ["title" => $rows[0]["location"]];
        error_log(json_encode($json));
    }