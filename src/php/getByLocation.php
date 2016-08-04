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
        $toDate = $toDate + 55250;
    }

    $params = array();
    foreach ($ids as $id){
        array_push($params,$id);
    }
    $toDate = 1469906230;
    $fromDate = 1469906220;
    $questionmarks = str_repeat("?,", count($params)-1) . "?";
    array_push($params, $toDate, $fromDate);
    error_log(json_encode($params));
    try {
        $stmt = DBConnection::instance()->prepare("SELECT capacity,P_Id FROM Location_Data WHERE P_Id IN ($questionmarks) AND timeStamp < ? AND timeStamp > ? ");
        $stmt->execute($params);
    } catch(Exception $e) {
        error_log("Error: ") . $e.getMessage();
    }

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $rows[] = $row;
    }
    error_log(json_encode($rows));
    echo json_encode($rows);

    foreach($rows as $row){
        $capacity[] = (int) $row["capacity"];
    }
    organizeData($rows,$ids);
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
function organizeData($data,$ids){
    $objArr = array();

    foreach($ids as $id){
        $index = 0;
        // Create data object
        $obj = new dataObj();
        // Set id
        $obj->id = $id;
        // While the index is less than the data length
        while($index < count($data)){

            // If P_Id is equal to id, push the capacity to the object capacity array
            if($data[$index]["P_Id"] == $id){
                $obj->pushCapacity($data[$index]["capacity"]);
            }
            $index++;
        }
        // Push the object to the return array
        array_push($objArr, $obj);
    }
    error_log(json_encode($objArr));
}
class dataObj {
    public $id;
    public $capacity = array();

    public function pushCapacity($val){
        array_push($this->capacity,$val);
    }
}