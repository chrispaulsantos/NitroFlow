<?php

    require_once "database_connect.php";

    $stmt = DBConnection::instance()->prepare("SELECT * FROM Location_Data");
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        echo json_encode($row["current_capacity"]);
    }
