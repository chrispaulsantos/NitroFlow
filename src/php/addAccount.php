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

    $UIDS = createUID($acct);

    foreach($UIDS as $UID){
        try {
            $stmt = DBConnection::instance()->prepare("INSERT INTO UnregisteredUID(P_Id,UID) VALUES(1,:UID)");
            $stmt->bindParam(":UID",$UID);
            $stmt->execute();
        } catch (Exception $e){
            error_log("Error: " . $e->getMessage());
        }
    }
    /**/


    function createUID($acct){
        $l = $acct["acctUnitCount"];

        for($i = 1; $i <= $l; $i++){

            $zip = $acct["acctZip"];
            if(strlen($zip) == 4){
                $zip = "0" . $zip;
            }

            //$vendor = $acct["acctVendor"];
            $vendor = "1";
            for($j = 0; strlen($vendor) < 4; $j++){
                $vendor = "0" . $vendor;
            }

            $unitNum = strtoupper(dechex($i));
            for($j = 0; strlen($unitNum) < 4; $j++){
                $unitNum = "0" . $unitNum;
            }

            $UIDS[] = "\$UID$" . $zip . $vendor . $unitNum;
            error_log("\$UID$" . $zip . $vendor . $unitNum);
        }
        return $UIDS;
            // error_log(json_encode($UIDS));
    }