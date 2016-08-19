<?php

    require_once "database_connect.php";

    $stmt = null;
    $locations = [];
    $rows = [];

    // Region passed from js
    if( $_GET != null ) {
        $region = $_GET['region'];
    }

    if($region == "ALL"){
        $query = "SELECT * FROM Current_Data INNER JOIN Locations ON Current_Data.P_Id = Locations.P_Id ORDER BY Current_Data.P_Id";
    } else {
        $query = "CALL `flow_data`.`getByRegion`(:reg)";
    }

    // Prepare the query for execution
    try {
        $stmt = DBConnection::instance()->prepare($query);
        $stmt->bindParam(":reg", $region);
        $stmt->execute();
    } catch(Exception $e){
        error_log("Error: " .$e->getMessage());
    }

    // Fetch the returned values
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $rows[] = $row;
        //error_log(json_encode($row));
    }

    foreach($rows as $row){
        // Create new location object to be returned to the js for processing
        $location = new location();
        $location->id = $row["P_Id"];
        $location->location = $row["AccStrAdd"];

        // Check to ensure the returned value is a number
        if($row["capacity"] != null || $row["capacity"] != ""){
            $location->current_capacity = $row["capacity"];
        } else {
            $location->current_capacity = 0;
        }

        $locations[] = $location;
    }

    echo json_encode($locations);

class location {
    public $id;
    public $location;
    public $current_capacity;
    public $time;
}
