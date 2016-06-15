<?php

    require_once "database_connect.php";
    require "location_object.php";

    // Array of id's gotten from the js responsible for hitting the database
    $ids = null;
    if( $_GET != null ) {
        $ids = $_GET['ids'];
    }
    $ids = array(1, 2, 3);

    $query = "SELECT Locations.P_Id, Locations.location, Location_Data.current_capacity, Location_Data.time_stamp ".
             "FROM Locations JOIN Location_Data ON Locations.P_Id = Location_Data.P_Id ".
             "WHERE Location_Data.time_stamp = (SELECT MAX(Location_Data.time_stamp) ".
             "FROM Location_Data WHERE Location_Data.P_Id = :id) AND Locations.P_Id = :id";
    $locations = [];

    // Loop over the selected id's and execute the query for each id
    foreach($ids as $id){
        error_log($id);
        // Prepare the query for execution
        try {
            $stmt = DBConnection::instance()->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
        } catch(Exception $e){
            error_log("Error: " .$e->getMessage());
        }

        /**
         * One row should be returned, if more than one, select the last value in the rows returned, this value
         * should always be the lowest value, since the query is last timestamp based.
         */
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $rows[] = $row;
        }
        if(count($rows) > 1){
            $row = $rows[count($rows)-1];
        }

        // Create new location object to be returned to the js for processing
        $location = new location();
        $location->id = $row["P_Id"];
        $location->location = $row["location"];
        $location->current_capacity = $row["current_capacity"];
        $location->time = $row["time_stamp"];
        error_log(json_encode($location));
        $locations[] = $location;
    }

    echo json_encode($locations);
    //error_log(json_encode($locations));
