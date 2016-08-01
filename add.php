<?php
    require_once "src/php/database_connect.php";
    session_start();
    error_log("Beginning login check.");

    // Check if user token is set
    if(isset($_SESSION["user_token"])) {

        // Check login token against database
        $login_token = $_SESSION["user_token"];
        $query = "SELECT * FROM `Users` WHERE `user_login_token` = :token AND `Users`.P_Id = 1 OR `Users`.P_Id = 2";
        try {
            $stmt = DBConnection::instance()->prepare($query);
            $stmt->bindParam(":token", $login_token);
            $stmt->execute();
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
        }

        // If no result returned, die, else login
        if ($stmt == false) {
            die;
        } else {
            error_log("Word you're logged in as user: " . $_SESSION["user_id"]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $user = $row["username"];
            error_log($user);
        }
        // If user token is not set, redirect to login page
    } else {
        echo "Please login, redirecting...";
        sleep(2);
        header("Location: http://159.203.186.131/login.php"); /* Redirect browser */
        exit();
    }

?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="src/css/Semantic/semantic.min.css">
        <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
        <script src="src/includes/papaparse.min.js"></script>
        <script src="src/css/Semantic/semantic.min.js"></script>
        <script src="src/js/add.js"></script>
    </head>
    <body>
        <label for="file" class="ui icon button" style="margin-top: 1px;">
            <i class="file icon"></i>
            Select CSV</label>
        <input type="file" id="file" style="display:none">
    </body>
</html>
