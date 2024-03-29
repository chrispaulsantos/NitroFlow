<?php
    // Include database connection class
    require_once "database_connect.php";

    $args = $_GET["args"];
    $ct = 0;

    foreach($args as $arg){
        $time = time();
        // Insert into location data table
        $stmt = DBConnection::instance()->prepare("CALL `insertData`(:id,:capacity,:t)");
        $stmt->bindParam(":capacity",$args[$ct]["capacity"]);
        $stmt->bindParam(":id",$args[$ct]["id"]);
        $stmt->bindParam(":t",$time);
        $stmt->execute();

        $ct++;
    }

    
