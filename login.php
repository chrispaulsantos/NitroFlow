<?php
    require_once "src/php/database_connect.php";
    session_start();

    if(isset($_SESSION["user_token"])) {
        $login_token = $_SESSION["user_token"];
        $query = "SELECT * FROM `Users` WHERE `user_login_token` = :token";
        try {
            $stmt = DBConnection::instance()->prepare($query);
            $stmt->bindParam(":token", $login_token);
            $stmt->execute();
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
        }
        if ($stmt == false) {
            die;
        } else {
            error_log("Word you're logged in as user: " . $_SESSION["user_id"]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $user = $row["username"];
        }
    } else {
        echo "Please login, redirecting...";
        die;
    }
?>

<HTML>
    <head>
        <link rel='stylesheet' href='src/css/Semantic/semantic.min.css' type='text/css'/>
        <link rel='stylesheet' href='src/css/styles.css' type='text/css'/>

        <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
        <script src="src/css/Semantic/semantic.min.js" type="text/javascript"></script>
        <script src="src/js/login.js" type="text/javascript"></script>

        <title> Nitro Flow </title>
    </head>
    <body>
        <div class="ui container center aligned" style="width:300px;">
            <div class="ui fluid middle aligned card">
                <div class="content">
                    <form class="ui left aligned form">
                        <div class="field">
                            <label>User Name</label>
                            <input name="username" placeholder="User Name" type="text">
                        </div>
                        <div class="field">
                            <label>Password</label>
                            <input name="password" placeholder="Password" type="password">
                        </div>
                        <button id="login_button" class="ui button" type="button">Login</button>
                    </form>
                </div>
            </div>
        </div>


        <script>
            $('.ui.dropdown')
                .dropdown()
            ;
            // console.log(window.innerHeight);
            height = window.innerHeight;
            $('.ui.container').css("margin-top", height/2-(230/2));
        </script>
    </body>
</HTML>
