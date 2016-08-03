<?php

    require_once "database_connect.php";
    require "location_object.php";

    // Variables gotten from the js
    $ids = null;
    if( $_GET != null ) {
        $ids = $_GET['ids'];
        $fromDate = strtotime($_GET['fromDate']);
        $toDate = strtotime($_GET['toDate']);
    }
    $locations = [];

    if($fromDate == $toDate){
        $toDate = $toDate + 56250;
    }

    //error_log($fromDate . " - " . $toDate);

    try {
        $stmt = DBConnection::instance()->prepare("SELECT capacity FROM Location_Data WHERE P_Id = 1 AND timeStamp < $toDate AND timeStamp > $fromDate");
        $stmt->execute();
    } catch(Exception $e) {
        error_log("Error: ") . $e.getMessage();
    }

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $rows[] = $row;
    }

    foreach($rows as $row){
        $capacity[] = (int) $row["capacity"];
    }


    //echo json_encode(sumEveryN($capacity));
echo json_encode($capacity);

function sumEveryN($data){
    $avgs = [];
    $i = 0;
    while($i < count($data)){
        $sum = 0;
        for($j = 0; $j < 10; $j++){
            $sum += $data[$i];
            $i++;
            error_log($sum);
        }
        $avgs[] = $sum / 10;
    }
    return $avgs;
}