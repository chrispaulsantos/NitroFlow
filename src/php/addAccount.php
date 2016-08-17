<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 8/15/16
 * Time: 7:17 AM
 */

    require_once "database_connect.php";

    if($_GET != null){
        $tempAcc = $_GET["acc"];

        // Create new account object
        $acc = new account();
        $acc->accName      = $tempAcc["accName"];
        $acc->accStrAdd    = $tempAcc["accAddress"];
        $acc->accAptNum    = $tempAcc["accAptNum"];
        $acc->accState     = $tempAcc["accState"];
        $acc->accZip       = $tempAcc["accZip"];
        $acc->accUnitCount = $tempAcc["AccUnitCount"];
    }

    $accId = $acc->insertAccount();
    error_log($accId);



    /*$UIDS = createUID($acct);

    foreach($UIDS as $UID){
        try {
            $stmt = DBConnection::instance()->prepare("INSERT INTO UnregisteredUID(P_Id,UID) VALUES(1,:UID)");
            $stmt->bindParam(":UID",$UID);
            $stmt->execute();
        } catch (Exception $e){
            error_log("Error: " . $e->getMessage());
        }
    }
    /**/ //INSERT UNREG IDS


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

    class account {
        public $accName;
        public $accStrAdd;
        public $accAptNum;
        public $accState;
        public $accZip;
        public $accUnitCount;

        public function insertAccount(){
            $query = "INSERT INTO `Locations`(AccName`, `AccZip`, `AccStrAdd`, `AccAptNum`, `AccState`, `AccUnits`)
                      VALUES(:accname,:acczip,:accstradd,:accaptnum,:accstate,:accunits";
            try {
                $stmt = DBConnection::instance()->prepare($query);
                $stmt->bindParam(":accname",$accName);
                $stmt->bindParam(":acczip",$accZip);
                $stmt->bindParam(":accstradd",$accStrAdd);
                $stmt->bindParam(":accaptnum",$accAptNum);
                $stmt->bindParam(":accstate",$accState);
                $stmt->bindParam(":accunits",$accUnitCount);
                $stmt->execute();
                return $stmt->lastInsertId();
            } catch (Exception $e){
                error_log("Error: " . $e->getMessage());
            }
        }
    }