<?php
    require_once "database_connect.php";

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
                $stmt->bindParam(":accname", $this->accName);
                $stmt->bindParam(":acczip",$this->accZip);
                $stmt->bindParam(":accstradd",$this->accStrAdd);
                $stmt->bindParam(":accaptnum",$this->accAptNum);
                $stmt->bindParam(":accstate",$this->accState);
                $stmt->bindParam(":accunits",$this->accUnitCount);
                $stmt->execute();

                // Get last insert id
                $this->accId = strtoupper(dechex($this->dbh->lastInsertId()));
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
        public function checkIfExists(){
            error_log("Checking");
            $query = "SELECT EXISTS(SELECT * FROM Locations WHERE accStrAdd = :stradd, accZip = :zip, accAptNum = :aptnum)";
            try {
                $stmt = $this->dbh->prepare($query);
                $stmt->bindParam(":stradd", $this->accStrAdd);
                $stmt->bindParam(":zip", $this->accZip);
                $stmt->bindParam(":aptnum",$this->accAptNum);
                $stmt->execute();
            } catch (Exception $e){
                error_log("Error: " . $e->getMessage());
            }

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                error_log("In the while");
                error_log(json_encode($row));
            }
        }
    }