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
        $acc->getUIDNumber();
    } catch (Exception $e){
        error_log("Error: " > $e->getMessage());
    }
