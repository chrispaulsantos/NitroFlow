<?php

    require_once "database_connect.php";
    require "location_object.php";

    $stmt = DBConnection::instance()->prepare("SELECT DISTINCT `P_Id` FROM `Location` ORDER BY `time_stamp` DESC");
    error_log($stmt->execute());



    $locs = [];

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        error_log("In the loop");

        /*$location = new location();
        $location->id = $row["P_Id"];
        $location->location = $row["location"];
        error_log(json_encode($location));
        $locs[] = $location;*/
    }
    //error_log(json_encode($locs));
