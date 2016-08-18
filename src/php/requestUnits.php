<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 8/18/16
 * Time: 2:07 PM
 */

    require_once "database_connect.php";
    require "accountObj.php";

    try {
        $acc = new account();
        $acc->accZip = "02210";
        $acc->accUnitCount = 15;
        $acc->accId = dechex(72);

        $acc->getNextUID();
        $acc->createUIDs();
        $acc->insertUnregisteredUIDs();
    } catch (Exception $e){
        error_log("Error: " > $e->getMessage());
    }
