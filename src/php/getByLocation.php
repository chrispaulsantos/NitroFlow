<?php

    require_once "database_connect.php";
    require "location_object.php";

    // Array of id's gotten from the js responsible for hitting the database
    $ids = null;
    if( $_GET != null ) {
        $ids = $_GET['ids'];
        $fromDate = strtotime($_GET['fromDate']);
        $toDate = strtotime($_GET['toDate']);
    }
    $locations = [];

    if($fromDate == $toDate){
        $toDate = $toDate + 60;
    }

    error_log($fromDate ." - ".$toDate);

    try {
        $stmt = DBConnection::instance()->prepare("SELECT capacity FROM Location_Data WHERE P_Id = 1 AND timeStamp < $toDate AND timeStamp > $fromDate");
        $stmt->execute();
    } catch(Exception $e) {
        error_log("Error: ") . $e.getMessage();
    }

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $rows = $row;
    }
    error_log(json_encode($rows));

    // Loop over the selected id's and execute the query for each id
    /*foreach($ids as $id){
        // error_log($id);
        // Prepare the query for execution
        try {
            $stmt = DBConnection::instance()->prepare("CALL `getByLocation`(:id)");
            $stmt->bindParam(":id", $id);
            $stmt->execute();
        } catch(Exception $e){
            error_log("Error: " .$e->getMessage());
        }

        /**
         * One row should be returned, if more than one, select the last value in the rows returned, this value
         * should always be the lowest value, since the query is last timestamp based.
         *
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $rows[] = $row;
        }

        // Check if more than one row existed at a given timestamp
        if(count($rows) > 1){
            $row = $rows[count($rows)-1];
        } else {
            $row = $rows[0];
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
    }*/

    echo json_encode($locations);
