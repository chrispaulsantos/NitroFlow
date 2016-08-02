<?php

    require_once "database_connect.php";
    require "location_object.php";

    $stmt = null;
    $locations = [];

    // Region passed from js
    if( $_GET != null ) {
        $region = $_GET['region'];
    }
    error_log($region);

    // error_log($id);
    // Prepare the query for execution
    try {
        $stmt = DBConnection::instance()->prepare("CALL `getByRegion`(:reg)");
        $stmt->bindParam(":reg", $region);
        $stmt->execute();
    } catch(Exception $e){
        error_log("Error: " .$e->getMessage());
    }

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
