<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 6/22/16
 * Time: 2:25 PM
 */

    require_once "database_connect.php";

// Check if the session is started and then login
    if ( is_session_started() == FALSE ){
        $stmt = null;
        $rows = array();

        // Check if username and password are null
        if( $_GET != null ){
            $username = $_GET["username"];
            $password = $_GET["password"];
        } else {
            throw new Exception("Username and password not set");
        }

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
            session_set_cookie_params(120);

            session_start();
            $_SESSION["user_token"] = generateToken($username);
            $_SESSION["user_id"] = $user_id;

            // error_log(json_encode(session_get_cookie_params()));
            // error_log($_SESSION["user_token"]);

            // Return redirect
            echo "SUCCESS";
        } else {
            // Return redirect
            echo "FAILURE";
        }
    } else {
        // FINISH LATER BUT USED FOR CHECKING CURRENT SESSION
        checkToken();
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
    function checkToken(){
        // Check the token against the database
        try{
            $stmt = DBConnection::instance()->prepare("SELECT `user_login_token` FROM `Users` WHERE `username` = :nm");
            $stmt->bindParam(":nm", $username);
            $stmt->execute();
        } catch (Exception $e){
            error_log("Error: " . $e->getMessage());
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