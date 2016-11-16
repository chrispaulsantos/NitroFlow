<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 8/29/16
 * Time: 6:50 PM
 */

session_start();
// Set timeout period in seconds
$inactive = $_SESSION["lifetime"];
error_log($inactive);
// Check to see if $_SESSION['timeout'] is set
if(isset($_SESSION['timeout']) ) {
    $session_life = time() - $_SESSION['timeout'];
    error_log($session_life);
    if($session_life > $inactive) {
        session_destroy();
        echo "FAIL";
    }
}