<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 6/22/16
 * Time: 2:25 PM
 */

    require_once "database_connect.php";

    if ( is_session_started() == FALSE ){
        $stmt = null;
        $rows = array();

        if( $_GET != null ){
            $username = $_GET["username"];
            $password = $_GET["password"];
        } else {
            throw new Exception("You suck.");
        }

        try{
            $stmt = DBConnection::instance()->prepare("SELECT * FROM `Users` WHERE `username` = :nm");

            $stmt->bindParam(":nm", $username);

            $stmt->execute();
        } catch (Exception $e){
            error_log("Error: " . $e->getMessage());
        }

        while( $row = $stmt->fetch(PDO::FETCH_ASSOC) ){
            $rows[] = $row;
        }

        if(count($rows) > 1){
            throw new Exception("Returned more than one user, contact administrator");
        } else if (count($rows) != 1){
            throw new Exception("No user found");
        } else {
            $pass_hash = $rows[0]["hash"];
            $user_id = $rows[0]["P_Id"];
            $check = password_verify($password, $pass_hash);
        }

        if($check){
            session_set_cookie_params(120);

            session_start();
            $_SESSION["user_token"] = generateToken($username);
            $_SESSION["user_id"] = $user_id;
            error_log(json_encode(session_get_cookie_params()));
            error_log($_SESSION["user_token"]);
            echo "REDIRECT";
        } else {
            echo "Wrong password.";
        }
    }

    function generateToken($username){
        $login_token = md5(uniqid(rand(), true));
        $query = "UPDATE `Users` SET `user_login_token` = :token WHERE `username` = :nm";

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
        try{
            $stmt = DBConnection::instance()->prepare("SELECT `user_login_token` FROM `Users` WHERE `username` = :nm");
            $stmt->bindParam(":nm", $username);
            $stmt->execute();
        } catch (Exception $e){
            error_log("Error: " . $e->getMessage());
        }
    }
    function is_session_started(){
        if ( php_sapi_name() !== 'cli' ) {
            if ( version_compare(phpversion(), '5.4.0', '>=') ) {
                return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
            } else {
                return session_id() === '' ? FALSE : TRUE;
            }
        }
        return FALSE;
    }