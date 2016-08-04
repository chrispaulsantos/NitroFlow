<?php

    require_once "database_connect.php";
    require "location_object.php";

    // Variables gotten from the js
    $locations = [];
    $ids = null;
    if( $_GET != null ) {
        $ids = $_GET['ids'];
        $fromDate = strtotime($_GET['fromDate']);
        $toDate = strtotime($_GET['toDate']);
    }
    if($fromDate == $toDate){
        $toDate = $toDate + 56250;
    }

    $ids = implode(',',$ids);
    error_log($ids);
    //error_log($fromDate . " - " . $toDate);

    try {
        $stmt = DBConnection::instance()->prepare("SELECT capacity,P_Id FROM Location_Data WHERE P_Id IN (:ids) AND timeStamp < :to AND timeStamp > :from");
        $stmt->bindParam(":ids", $ids);
        $stmt->bindParam(":from",$fromDate);
        $stmt->bindParam(":to",$toDate);
        $stmt->execute();
    } catch(Exception $e) {
        error_log("Error: ") . $e.getMessage();
    }

    while ($row = $stmt->fetchAll()){
        $rows[] = $row;
    }

    echo json_encode($rows);

    foreach($rows as $row){
        $capacity[] = (int) $row["capacity"];
    }


    //echo json_encode(sumEveryN($capacity,getN(count($capacity))));

function getN($points){
    $n = 0;
    switch ($points) {
        case $points < 10:
            $n = 1;
            break;
        case $points < 100 && $points >= 10:
            $n = 1;
            break;
        case $points < 1000 && $points >= 100:
            $n = 10;
            break;
        case $points < 10000 && $points >= 1000:
            $n = 50;
            break;
        case $points < 100000 && $points >= 10000:
            $n = 100;
            break;
        case $points < 1000000 && $points >= 100000:
            $n = 1000;
            break;
        default:
            error_log("In default for some reason");// "NODATE";
    }
    error_log($n);
    return $n;
}
function sumEveryN($data,$n){
    $avgs = [];
    $i = 0;
    while($i < count($data)){
        $sum = 0;
        for($j = 0; $j < $n; $j++){
            if(!isset($data[$i])){
                $sum += 0;
                $i++;
            } else {
                $sum += $data[$i];
                $i++;
            }
        }
        $avgs[] = $sum / $n;
    }
    return $avgs;
}