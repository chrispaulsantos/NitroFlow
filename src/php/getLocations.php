<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 8/2/16
 * Time: 8:55 AM
 */

    require_once "database_connect.php";

    $stmt = DBConnection::instance()->prepare("SELECT DISTINCT `location` FROM `Locations`");
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $rows[] = $row;
    }


    createJSON($rows);

    function createJSON($rows){
        error_log(json_encode($rows));
    }