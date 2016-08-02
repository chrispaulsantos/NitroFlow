<?php

    require_once "database_connect.php";
    require "location_object.php";

    $stmt = null;
    $locations = [];
    $rows = [];

    // Region passed from js
    if( $_GET != null ) {
        $region = $_GET['region'];
    }

    // Prepare the query for execution
    try {
        $query = "SELECT `Current_Data`.`P_Id`, `Current_Data`.`capacity`, `Locations`.`region`, `Locations`.`location` FROM `flow_data`.`Current_Data` INNER JOIN `flow_data`.`Locations` ON `Current_Data`.`P_Id` = `Locations`.`P_Id` WHERE `Locations`.`region` = :reg";
        //$stmt = DBConnection::instance()->prepare($query);
        $stmt = DBConnection::instance()->prepare("CALL `flow_data`.`getByRegion`(:reg)");
        $stmt->bindParam(":reg", $region);
        $stmt->execute();
    } catch(Exception $e){
        error_log("Error: " .$e->getMessage());
    }

    error_log(json_encode($row = $stmt->fetch(PDO::FETCH_ASSOC)));
    // Fetch the returned values
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $rows[] = $row;
        error_log(json_encode($row));
    }

    // Create new location object to be returned to the js for processing
    $location = new location();
    $location->id = $row["P_Id"];
    $location->location = $row["location"];

    // Check to ensure the returned value is a number
    if($row["capacity"] != null || $row["capacity"] != ""){
        $location->current_capacity = $row["capacity"];
    } else {
        $location->current_capacity = 0;
    }

    $location->time = $row["timeStamp"];
    $locations[] = $location;

    echo json_encode($locations);
