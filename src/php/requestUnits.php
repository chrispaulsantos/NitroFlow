<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 8/18/16
 * Time: 2:07 PM
 */

    require_once "database_connect.php";
    require "accountObj.php";

    if($_GET != null){
        $VAR = $_GET["acc"];
        $acc = new account();
        $acc->requestedUnits = $VAR["units"];
        $acc->updateAccount($VAR["accId"]);
    }


