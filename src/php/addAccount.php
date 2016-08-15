<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 8/15/16
 * Time: 7:17 AM
 */

    require_once "database_connect.php";

    if($_GET != null){
        $acct = $_GET["acct"];
    }

    $UIDS = array();
    $numIDs = $acct["acctUnitCount"];

    for($i = 0; $i < $numIDs; $i++){
        $UIDS[] = createUID($acct, $i);
    }
    error_log(json_encode($UIDS));


    try {
        $stmt = DBConnection::instance()->prepare("INSERT INTO UnregisteredUID(UID) VALUES(:UID)");
        $stmt->bindParam(":UID",$UID);
        $stmt->execute();
    } catch (Exception $e){
        error_log("Error: " . $e->getMessage());
    }


    function createUID($acct, $unitNum){
    $l = count($acct);

    for($i = 0; $i < $l; $i++){

        $zip = $acct["Zip"];
        if(strlen($zip) == 4){
            $zip = "0" . $zip;
        }

        $vendor = $acct["Vendor"];
        for($j = 0; strlen($vendor) < 4; $j++){
            $vendor = "0" . $vendor;
        }

        for($j = 0; strlen($unitNum) < 4; $j++){
            $unitNum = "0" . $unitNum;
        }

        $UID = "\$UID$" . $zip . $vendor . $unitNum;
        error_log($UID);
    }
}