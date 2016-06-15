<?php

    require_once "database_connect.php";
    require "location_object.php";



    $args = [];
    $args[0] = array('key'=>'`location`','value'=>"'200 Seaport Blvd'");
    $args[1] = array('key'=>'`location`','value'=>"'245 Summer Street'");
    if( $_GET != null ) {
        $args = $_GET['args'];
    }
    $id = 1;

    $query = "SELECT Locations.P_Id, Locations.location, Location_Data.current_capacity, Location_Data.time_stamp".
             "FROM Locations JOIN Location_Data ON Locations.P_Id = Location_Data.P_Id".
             "WHERE Location_Data.time_stamp = (SELECT MAX(Location_Data.time_stamp)".
             "FROM Location_Data WHERE Location_Data.P_Id = :id) AND Locations.P_Id = :id";

    try {
        $stmt = DBConnection::instance()->prepare($query);
    } catch(Exception $e){
        error_log("Error: " .$e->getMessage());
    }

    $stmt->bindParam(":id", $id);

    try {
        $stmt->execute();
    } catch(Exception $e){
        error_log("Error: " . $e->getMessage());
    }

    $locations = [];

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $location = new location();
        $location->id = $row["P_Id"];
        $location->location = $row["location"];
        $location->current_capacity = $row["current_capacity"];
        $locations[] = $location;
    }
    error_log(json_encode($locations));

    /**
     * @param $args
     * @return string
     * This function constructs a SQL query built off of multiple WHERE parameters. If there is one value in args, it
     * will return only one WHERE. If there is more than one object in args, it will construct a query containing
     * multiple WHERE's, and append an AND where it is needed.
     */
    function constructQuery( $baseQuery, $args, $extendQuery ) {

        // Base query (SELECT ALL)
        $query = $baseQuery;
        $ct = 0;

        // If only one value, return query with one value, else return multiple WHERE query
        if( count( $args) == 1) {
            $query .= "WHERE " . $args[0]["key"] . " = " . $args[0]["value"];
        } else if( count( $args) > 1) {
            $query .= "WHERE ";
            for($ct = 0; $ct < count($args); $ct++){
                $query .= $args[$ct]["key"] . " = " . $args[$ct]["value"];

                // Appends an AND up until ct - 1 (Since no AND is needed after the last WHERE
                if($ct < count( $args) - 1){
                    $query .= " AND ";
                }
            }
        }

        // Return the constructed query
        return $query . $extendQuery;
    }
