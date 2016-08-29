<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 6/22/16
 * Time: 2:25 PM
 */

    require_once "/var/www/html/NitroFlow/src/php/database_connect.php";

    // Check if username and password are null
    if( $_GET != null ){
        $username = $_GET["username"];
        $password = $_GET["password"];
    } else {
        throw new Exception("Username and password not set");
    }

// Check if the session is started and then login
    if ( is_session_started() == FALSE ){
        $stmt = null;
        $rows = array();

        // Check database to see if the username exists
        try{
            $stmt = DBConnection::instance()->prepare("SELECT * FROM `Users` WHERE `username` = :nm");
            $stmt->bindParam(":nm", $username);
            $stmt->execute();
        } catch (Exception $e){
            error_log("Error: " . $e->getMessage());
        }

        // Load returned values into array
        while( $row = $stmt->fetch(PDO::FETCH_ASSOC) ){
            $rows[] = $row;
        }

        // Check to ensure that only one instance of a username is returned
        if(count($rows) > 1){
            throw new Exception("Returned more than one user, contact administrator");
        } else if (count($rows) != 1){
            throw new Exception("No user found");
        } else {
            $pass_hash = $rows[0]["hash"];
            $user_id = $rows[0]["P_Id"];
            $check = password_verify($password, $pass_hash);
        }

        // If password_verify returns true, begin session
        if($check){
            // Set cookie life to 120 seconds and start session
            if($user_id <= 5){
                session_set_cookie_params(3600);
            } else {
                session_set_cookie_params(120);
            }

            session_start();
            $_SESSION["user_token"] = generateToken($username);
            $_SESSION["user_id"] = $user_id;
            $_SESSION['timeout'] = time();

            // Return redirect response
            echo "SUCCESS";
        } else {
            // Return redirect response
            echo "FAILURE";
        }
    } else {
        session_start();
        if(checkToken($_SESSION["user_token"],$username)){
            echo "SUCCESS";
        } else {
            echo "FAILURE";
        }
    }

    function generateToken($username){
        // Generate a random login token
        $login_token = md5(uniqid(rand(), true));
        $query = "UPDATE `Users` SET `user_login_token` = :token WHERE `username` = :nm";

        // Update user login token
        try{
            $stmt = DBConnection::instance()->prepare($query);
            $stmt->bindParam(":nm", $username);
            $stmt->bindParam(":token", $login_token);
            $stmt->execute();
        } catch (Exception $e){
            error_log("Error: " . $e->getMessage());
        }

        return $login_token;
    }
    function checkToken($token,$username){
        // Check the token against the database
        try{
            $stmt = DBConnection::instance()->prepare("SELECT * FROM `Users` WHERE `username`=:nm AND `user_login_token`=:tk");
            $stmt->bindParam(":nm", $username);
            $stmt->bindParam("tk", $token);
            $stmt->execute();
        } catch (Exception $e){
            error_log("Error: " . $e->getMessage());
        }
        // Check if there were results
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return true if a result was returned, else false
        if($row != FALSE){
            return TRUE;
        } else {
            return FALSE;
        }
    }
    function is_session_started(){
        // Check if th session is started
        if ( php_sapi_name() !== 'cli' ) {
            if ( version_compare(phpversion(), '5.4.0', '>=') ) {
                return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
            } else {
                return session_id() === '' ? FALSE : TRUE;
            }
        }
        return FALSE;
    }