<?php

    require_once "database_connect.php";
    require "location_object.php";

    $stmt = DBConnection::instance()->prepare("SELECT * FROM Location_Data");
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $location = new location();
        $location->id = $row["P_Id"];
        $location->location = $row["current_capacity"];

        $locs[] = $location;
    }
    error_log($locs);
