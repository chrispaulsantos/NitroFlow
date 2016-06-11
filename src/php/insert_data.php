<?php
    require_once "database_connect.php";

    $capacity = $_GET["capacity"];

    $stmt = DBConnection::instance()->prepare("INSERT INTO Location_Data(P_Id,current_capacity) VALUES(1,:capacity)");
    $stmt->bindParam(":capacity",$capacity);
    $stmt->execute();
