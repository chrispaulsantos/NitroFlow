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

    /*
     * Since the dates are given in the format mm/dd/yyyy, it will cause the day to start at 00:00, no matter what, so
     * to avoid no data being returned, add hours to the dates so that the query returns data
     */
    $fromDate = $fromDate + 28800; // 08:00
    $toDate = $toDate + 86400; // 24:00
    error_log("From: " .$fromDate . " To: " . $toDate);

    $params = array();
    foreach ($ids as $id){
        array_push($params,$id);
    }

    $questionmarks = str_repeat("?,", count($params)-1) . "?";
    array_push($params, $toDate, $fromDate);

    $query = "SELECT `Location_Data`.capacity,`Location_Data`.P_Id, `Locations`.location FROM Location_Data INNER JOIN Locations ON Locations.P_Id = Location_Data.P_Id WHERE Location_Data.P_Id IN ($questionmarks) AND timeStamp < ? AND timeStamp > ?";
    try {
        $stmt = DBConnection::instance()->prepare($query);
        $stmt->execute($params);
    } catch(Exception $e) {
        error_log("Error: ") . $e.getMessage();
    }

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $rows[] = $row;
    }

    // Echo processed data
    $points = count($rows)/count($ids);
    $n = getN($points);
    echo json_encode(getEveryN(organizeData($rows,$ids),$n));


function getEveryN($objs,$n){
    $arr = array();

    foreach ($objs as $obj){
        $tmp = array();

        for($i = 0; $i < count($obj->capacity); $i++){
            if($i%$n == 0){
                $tmp[] = $obj->capacity[$i];
            }
        }
        $obj->capacity = $tmp;
        $arr[] = $obj;
    }
    return $arr;

}
function getN($points){
    $n = 0;
    switch ($points) {
        case $points < 10:
            $n = 1;
            break;
        case $points < 100 && $points >= 10:
            $n = 5;
            break;
        case $points < 1000 && $points >= 100:
            $n = 25;
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
                $obj->location = $data[$index]["location"];
                $obj->pushCapacity($data[$index]["capacity"]);
            }
            $index++;
        }
        // Push the object to the return array
        array_push($objArr, $obj);
    }
    return $objArr;
}

class dataObj {
    public $id;
    public $location;
    public $capacity = array();

    public function pushCapacity($val){
        array_push($this->capacity,$val);
    }
}