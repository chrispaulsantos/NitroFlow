<?php
    require_once "src/php/database_connect.php";
    session_start();
    error_log("Beginning login check.");

    // Check if user token is set
    if(isset($_SESSION["user_token"])) {

        // Check login token against database
        $query = "SELECT * FROM `Users` WHERE `user_login_token` = :token";
        try {
            $stmt = DBConnection::instance()->prepare($query);
            $stmt->bindParam(":token", $_SESSION["user_token"]);
            $stmt->execute();
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
        }

        // If no result returned, die, else login
        if ($stmt == false) {
            die;
        } else {
            error_log("Word, you're logged in as user: " . $_SESSION["user_id"]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $user = $row["username"];
            // error_log($user);
        }
     // If user token is not set, redirect to login page
    } else {
        // echo "Please login, redirecting...";
        sleep(1);
        header("Location: http://159.203.186.131/login.php"); /* Redirect browser */
        exit();
    }


    $regions = array();

    try {
        $stmt = DBConnection::instance()->prepare("SELECT DISTINCT region FROM Locations");
        $stmt->execute();
    } catch (Exception $e){
        error_log("Error: " .$e->getMessage());
    }

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $regions[] = $row["region"];
    }
?>

<html>
    <head>
        <link rel='stylesheet' href='src/css/Semantic/semantic.min.css' type='text/css'/>

        <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
        <script type="text/javascript" src="src/js/Chart.js"></script>
        <script src="src/js/get_data.js" type="text/javascript"></script>
        <script src="src/css/Semantic/semantic.min.js" type="text/javascript"></script>
        <script src="src/js/logout.js" type="text/javascript"></script>
    </head>
    <body>

        <div class="ui menu">
            <div class="ui category search item">
                <div class="ui transparent icon input">
                    <input class="prompt" placeholder="Search Locations" type="text">
                    <i class="search link icon"></i>
                </div>
                <div class="results"></div>
            </div>
            <div class="ui right secondary menu">
                <div class="ui dropdown pointing item">
                    <i class="options icon"></i>
                    Account
                    <i class="dropdown icon"></i>
                    <div class="menu">
                        <div class="item"><?php echo $user ?></div>
                        <div class="item">Settings</div>
                        <div class="item">Database</div>
                        <div class="divider"></div>
                        <div class="item" id="logout">Logout</div>
                    </div>
                </div>
            </div>
        </div>

        <div id='content' class="ui container" style="height: 500px; width: 1000px;">

            <div class="ui grid">

                <div class="four wide column">
                    <div class="ui left aligned segment">
                        <select class="ui fluid scrolling search dropdown">
                            <option value="">Select Region</option>
                            <option value="ALL" selected>Select All Regions</option>
                            <?php foreach($regions as $region): ?>
                                <option value="'<?php echo $region; ?>'"><?php echo $region; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="alert" class="ui scrollable left aligned segment">
                        
                    </div>
                </div>
                <div class="twelve wide column">
                    <div class="ui right left segment">
                        <div class="ui segment">
                            <canvas id="chart" width="400" height="250"></canvas>
                        </div>
                        <div id="time">Last Updated: </div>
                    </div>
                </div>

            </div>

        </div>

        <script>
            $('#content').css("margin-top", window.innerHeight/2-(300));
            //$('#chart').attr("width", window.innerWidth*.7, "height", $('#content').height()*.6);
            $('.ui.dropdown').dropdown();

            var content = [
                    { title: 'Horse' },
                    { title: 'Cow'}
                ];
            $('.ui.search')
                .search({
                    source : content,
                    searchFields   : [
                        'title'
                    ],
                });
        </script>

    </body>
</html>
