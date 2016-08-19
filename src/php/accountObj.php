<?php
    require_once "database_connect.php";

    class account {
        public $accName;
        public $accStrAdd;
        public $accAptNum;
        public $accState;
        public $accZip;
        public $requestedUnits;
        private $currentUnits = null;
        private $accId;
        private $UIDS = array();
        private $dbh;

        function __construct(){
            return $this->dbh = DBConnection::instance();
        }

        public function checkIfExists(){
            // Check if account exists in database
            $query = "SELECT EXISTS(SELECT * FROM `Locations` 
                                    WHERE `AccStrAdd` = :stradd AND `AccZip` = :zip AND `AccAptNum` = :aptnum)";
            try {
                $stmt = $this->dbh->prepare($query);
                $stmt->bindParam(":stradd", $this->accStrAdd);
                $stmt->bindParam(":zip", $this->accZip);
                $stmt->bindParam(":aptnum",$this->accAptNum);
                $stmt->execute();
            } catch (Exception $e){
                error_log("Error: " . $e->getMessage());
            }

            $row = $stmt->fetch(PDO::FETCH_NUM);

            if($row[0] == "1"){
                return true;
            } else {
                return false;
            }
        }
        public function insertAccount(){
            // Insert Query
            $query = "INSERT INTO `Locations`(`AccName`, `AccZip`, `AccStrAdd`, `AccAptNum`, `AccState`, `AccUnits`)
                      VALUES(:accname,:acczip,:accstradd,:accaptnum,:accstate,:accunits)";
            try {
                // Insert into database
                $stmt = $this->dbh->prepare($query);
                $stmt->bindParam(":accname", $this->accName);
                $stmt->bindParam(":acczip",$this->accZip);
                $stmt->bindParam(":accstradd",$this->accStrAdd);
                $stmt->bindParam(":accaptnum",$this->accAptNum);
                $stmt->bindParam(":accstate",$this->accState);
                $stmt->bindParam(":accunits",$this->requestedUnits);
                $stmt->execute();

                // Get last insert id
                $this->accId = $this->dbh->lastInsertId();
            } catch (Exception $e){
                error_log("Error: " . $e->getMessage());
            }

            // Create UID's
            $this->createUIDs();
            // Insert created UID's
            $this->insertUnregisteredUIDs();
        }
        public function updateAccount($id){
            $this->accId = $id;
            $this->getAccInfo();
            $this->createUIDs();
            $this->insertUnregisteredUIDs();
            // We need to update the current unit count in the database
            $this->updateUnitCount();
        }
        private function createUIDs(){
            $start = null;

            /* If current units is null then we are inserting an account,
             * if it is not null we are updating an account
             */
            if($this->currentUnits == null){
                // Start at 1, go the number of requested units
                $start = 1;
                $l = $this->requestedUnits;
            } else {
                // Get the next UID number
                $this->getNextUID();
                // Start at current units + 1 and go to current+requested
                $start = $this->currentUnits + 1;
                $l = $this->requestedUnits + $this->currentUnits;
            }

            error_log("Start: " . $start . " End: " . $l);

            for($i = $start; $i <= $l; $i++){

                // Add a zero to the front of zip if the length is less than 4
                $zip = $this->accZip;
                if(strlen($zip) == 4){
                    $zip = "0" . $zip;
                }

                // Add zeroes to the front if length less than 4
                $vendorId = strtoupper(dechex($this->accId));
                for($j = 0; strlen($vendorId) < 4; $j++){
                    $vendorId = "0" . $vendorId;
                }

                // Convert id number to uppercase hex value, and add zeroes
                $unitNum = strtoupper(dechex($i));
                for($j = 0; strlen($unitNum) < 4; $j++){
                    $unitNum = "0" . $unitNum;
                }

                // Concatenate strings from above
                $this->UIDS[] = "\$UID$" . $zip . $vendorId . $unitNum;
                error_log("\$UID$" . $zip . $vendorId . $unitNum);
            }
        }
        private function insertUnregisteredUIDs(){
            // Insert each id into database
            foreach($this->UIDS as $UID){
                try {
                    $stmt = $this->dbh->prepare("INSERT INTO `UnregisteredUID`(`P_Id`,`UID`) VALUES(:vendorid,:UID)");
                    $stmt->bindParam(":vendorid",$this->accId);
                    $stmt->bindParam(":UID",$UID);
                    $stmt->execute();
                } catch (Exception $e){
                    error_log("Error: " . $e->getMessage());
                }
            }
        }
        private function getNextUID(){
            // Gets the start for the next UID
            try {
                $stmt = $this->dbh->prepare("SELECT AccUnits FROM Locations WHERE P_Id = :id");
                $stmt->bindParam(":id",$this->accId);
                $stmt->execute();
            } catch (Exception $e) {
                error_log("Error: " . $e->getMessage());
            }

            // Sets the current unit count
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->currentUnits = $row["AccUnits"];
        }
        private function updateUnitCount(){
            // Updates the current unit count in the database
            $units = $this->currentUnits + $this->requestedUnits;
            error_log("Updated Unit Count: " . $units);
            try {
                $stmt = $this->dbh->prepare("UPDATE `Locations`
                                             SET AccUnits = :units
                                             WHERE P_Id = :id");
                $stmt->bindParam(":units", $units);
                $stmt->bindParam(":id",$this->accId);
                $stmt->execute();
            } catch (Exception $e) {
                error_log("Error: " . $e->getMessage());
            }
        }
        private function getAccInfo(){
            // Gets the current account info
            try {
                $stmt = $this->dbh->prepare("SELECT * FROM `Locations` WHERE P_Id = :id");
                $stmt->bindParam(":id", $this->accId);
                $stmt->execute();
            } catch (Exception $e){
                error_log("Error: " . $e->getMessage());
            }

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->accName      = $row["AccName"];
            $this->accStrAdd    = $row["AccStrAdd"];
            $this->accAptNum    = $row["AccAptNum"];
            $this->accState     = $row["AccState"];
            $this->accZip       = $row["AccZip"];
            $this->currentUnits = $row["AccUnits"];
        }
    }