<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 8/29/16
 * Time: 6:50 PM
 */

session_start();
// set timeout period in seconds
$inactive = 120;
// check to see if $_SESSION['timeout'] is set
if(isset($_SESSION['timeout']) ) {
    $session_life = time() - $_SESSION['timeout'];
    if($session_life > $inactive) {
        session_destroy();
        echo "FAIL";
    }
}