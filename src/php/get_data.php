<?php

    require_once "database_connect.php";
    require "location_object.php";



    $args = [];
    $args[0] = array('key'=>'`location`','value'=>"'200 Seaport Blvd'");
    $args[1] = array('key'=>'`location`','value'=>"'245 Summer Street'");
    if( $_GET != null ) {
        $args = $_GET['args'];
    }

    error_log(json_encode($args));

    $query = constructQuery($args);
    error_log($query);

    try {
        $stmt = DBConnection::instance()->prepare($query);
    } catch(Exception $e){
        error_log("Error: " .$e->getMessage());
    }

    try {
        error_log(json_encode($stmt));
        error_log($stmt->execute());
    } catch(Exception $e){
        error_log("Error: " . $e->getMessage());
    }

    $locations = [];

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        error_log("In the while");
        error_log(json_encode($row));
        $location = new location();
        $location->id = $row["P_Id"];
        $location->location = $row["location"];
        $locations[] = $location;
    }

    /*$stmt = DBConnection::instance()->prepare("SELECT * FROM `Location_Data` ORDER BY `time_stamp` DESC");
    error_log($stmt->execute());

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        foreach($locations as $location){
            if($row["P_Id"] == $location->id){
                $location->current_capacity = $row["current_capacity"];
            }
        }
    }*/

    /**
     * @param $args
     * @return string
     * This function constructs a SQL query built off of multiple WHERE parameters. If there is one value in args, it
     * will return only one WHERE. If there is more than one object in args, it will construct a query containing
     * multiple WHERE's, and append an AND where it is needed.
     */
    function constructQuery( $args ) {

        // Base query (SELECT ALL)
        $query = "SELECT * FROM Locations ";
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
        return $query;
    }
