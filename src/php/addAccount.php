<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 8/15/16
 * Time: 7:17 AM
 */

    require "accountObj.php";

    if($_GET != null){
        $tempAcc = $_GET["acc"];

        // Create new account object
        $acc = new account();
        $acc->accName        = $tempAcc["accName"];
        $acc->accStrAdd      = $tempAcc["accAddress"];
        $acc->accAptNum      = $tempAcc["accAptNum"];
        $acc->accState       = $tempAcc["accState"];
        $acc->accZip         = $tempAcc["accZip"];
        $acc->requestedUnits = $tempAcc["accUnitCount"];

        // Check if the account exists, if not, insert
        if(!$acc->checkIfExists()){
            $acc->insertAccount();
        } else {
            echo "EXISTS";
        }

    }