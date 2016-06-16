<?php
    require_once "database_connect.php";

    $capacity = $_GET["capacity"];

    $args = $_GET["args"];
    error_log(json_encode($args));
    $ct = 0;

    foreach($args as $arg){
        $stmt = DBConnection::instance()->prepare("INSERT INTO Location_Data(P_Id,current_capacity) VALUES(:id,:capacity)");
        $stmt->bindParam(":capacity",$args[$ct]["capacity"]);
        $stmt->bindParam(":id",$args[$ct]["id"]);
        $stmt->execute();
        $ct++;
    }

    
