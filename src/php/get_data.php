<?php

    require_once "database_connect.php";

    $stmt = DBConnection::instance()->prepare("SELECT * FROM Location_Data");
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $location = new location();
        $location->id = $row["P_Id"];
        $location->location = $row["current_capacity"];

        $loc[] = $location;
    }
    echo json_encode($location);
