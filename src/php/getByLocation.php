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

    getN($fromDate,$toDate);

    //error_log($fromDate . " - " . $toDate);

    try {
        $stmt = DBConnection::instance()->prepare("SELECT capacity FROM Location_Data WHERE P_Id = $ids[0] AND timeStamp < $toDate AND timeStamp > $fromDate");
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
    echo json_encode(sumEveryN($capacity,getN($fromDate,$toDate)));

function getN($data){
    //$seconds = $to - $from;
    //$minutes = round($seconds) / 60;

    $points = count($data);
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
            $n = 100;
            break;
        case $points < 100000 && $points >= 10000:
            $n = 1000;
            break;
        case $points < 1000000 && $points >= 100000:
            $n = 10000;
            break;
        default:
            echo "NODATE";
            die;
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