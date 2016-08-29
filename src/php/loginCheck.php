<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 8/29/16
 * Time: 6:50 PM
 */

session_start();
// Get cookie info
$cookieInfo = session_get_cookie_params();
// Set timeout period in seconds
$inactive = $cookieInfo["lifetime"];
error_log(json_encode($inactive));
// Check to see if $_SESSION['timeout'] is set
if(isset($_SESSION['timeout']) ) {
    $session_life = time() - $_SESSION['timeout'];
    if($session_life > $inactive) {
        session_destroy();
        echo "FAIL";
    }
}