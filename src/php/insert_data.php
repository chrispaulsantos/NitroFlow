<?php
    // Include database connection class
    require_once "database_connect.php";

    $args = $_GET["args"];
    $ct = 0;

    foreach($args as $arg){
        // Insert into location data table
        $stmt = DBConnection::instance()->prepare("INSERT INTO Location_Data(P_Id,current_capacity) VALUES(:id,:capacity)");
        $stmt->bindParam(":capacity",$args[$ct]["capacity"]);
        $stmt->bindParam(":id",$args[$ct]["id"]);
        $stmt->execute();

        // Insert into current data table
        $stmt = DBConnection::instance()->prepare("CALL `insertCurrentData`(:capacity, :id)");
        $stmt->bindParam(":capacity",$args[$ct]["capacity"]);
        $stmt->bindParam(":id",$args[$ct]["id"]);
        $stmt->execute();

        $ct++;
    }

    
