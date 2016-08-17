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
        $acc->accUnitCount = $tempAcc["accUnitCount"];

        $acc->insertAccount();
        $acc->createUIDs();
        $acc->insertUnregisteredUIDs();
    }


    class account {
        public $accName;
        public $accStrAdd;
        public $accAptNum;
        public $accState;
        public $accZip;
        public $accUnitCount;
        public $accId;
        public $UIDS = array();
        public $dbh;

        function __construct(){
            return $this->dbh = DBConnection::instance();
        }

        public function insertAccount(){
            $query = "INSERT INTO `Locations`(`AccName`, `AccZip`, `AccStrAdd`, `AccAptNum`, `AccState`, `AccUnits`)
                      VALUES(:accname,:acczip,:accstradd,:accaptnum,:accstate,:accunits)";
            try {
                $stmt = $this->dbh->prepare($query);
                error_log(json_encode($stmt));
                $stmt->bindParam(":accname", $this->accName);
                $stmt->bindParam(":acczip",$this->accZip);
                $stmt->bindParam(":accstradd",$this->accStrAdd);
                $stmt->bindParam(":accaptnum",$this->accAptNum);
                $stmt->bindParam(":accstate",$this->accState);
                $stmt->bindParam(":accunits",$this->accUnitCount);
                $stmt->execute();

                // Get last insert id
                $this->accId = strtoupper(dechex($dbh->lastInsertId()));
            } catch (Exception $e){
                error_log("Error: " . $e->getMessage());
            }
        }
        public function createUIDs(){
            $l = $this->accUnitCount;

            for($i = 1; $i <= $l; $i++){

                $zip = $this->accZip;
                if(strlen($zip) == 4){
                    $zip = "0" . $zip;
                }

                $vendorId = $this->accId;
                for($j = 0; strlen($vendorId) < 4; $j++){
                    $vendorId = "0" . $vendorId;
                }

                $unitNum = strtoupper(dechex($i));
                for($j = 0; strlen($unitNum) < 4; $j++){
                    $unitNum = "0" . $unitNum;
                }

                $this->UIDS[] = "\$UID$" . $zip . $vendorId . $unitNum;
                error_log("\$UID$" . $zip . $vendorId . $unitNum);
            }
        }
        public function insertUnregisteredUIDs(){
            foreach($this->UIDS as $UID){
                try {
                    $stmt = $this->dbh->prepare("INSERT INTO UnregisteredUID(P_Id,UID) VALUES(:vendorid,:UID)");
                    $stmt->bindParam(":UID",$UID);
                    $stmt->bindParam(":vendorid",$this->accId);
                    $stmt->execute();
                } catch (Exception $e){
                    error_log("Error: " . $e->getMessage());
                }
            }
        }
    }