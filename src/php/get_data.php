<?php

    require_once "database_connect.php";
    require "location_object.php";

    $stmt = DBConnection::instance()->prepare("SELECT DISTINCT P_Id, current_capacity FROM Location_Data ORDER BY time_stamp DESC");
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $location = new location();
        $location->id = $row["P_Id"];
        $location->location = $row["current_capacity"];

        $locs[] = $location;
    }
    error_log(json_encode($locs));
