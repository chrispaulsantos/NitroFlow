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
    echo json_encode(sumEveryN($capacity,getN($fromDate,$toDate)));

function getN($from,$to){
    $seconds = $to - $from;
    $minutes = round($seconds) / 60;

    switch ($minutes) {
        case $minutes < 10:
            echo $minutes."<br>";
            $n = 1;
            echo $n;
            break;
        case $minutes < 100 && $minutes >= 10:
            echo $minutes."<br>";
            $n = 10;
            echo $n;
            break;
        case $minutes < 1000 && $minutes >= 100:
            echo $minutes."<br>";
            $n = 100;
            echo $n;
            break;
        case $minutes < 10000 && $minutes >= 1000:
            echo $minutes."<br>";
            $n = 1000;
            echo $n;
            break;
        case $minutes < 100000 && $minutes >= 10000:
            echo $minutes."<br>";
            $n = 10000;
            echo $n;
            break;
        case $minutes < 1000000 && $minutes >= 100000:
            echo $minutes."<br>";
            $n = 100000;
            echo $n;
            break;
        default:
            echo "NODATE";
            die;
    }
    return $n;
}
function sumEveryN($data,$n){
    $avgs = [];
    $i = 0;
    while($i < count($data)){
        $sum = 0;
        for($j = 0; $j < $n; $j++){
            if($data[$i] == null || $data[$i] == ""){
                $sum += 0;
                $i++;
                error_log($sum);
            } else {
                $sum += $data[$i];
                $i++;
                error_log($sum);
            }
        }
        $avgs[] = $sum / 10;
    }
    return $avgs;
}