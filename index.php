<?php
    require_once "src/php/database_connect.php";
    session_start();
/*
    if(isset($_SESSION["user_token"])){
        $login_token = $_SESSION["user_token"];

        $query = "SELECT * FROM `Users` WHERE `user_login_token` = :token";

        try{
            $stmt = DBConnection::instance()->prepare($query);
            $stmt->bindParam(":token", $login_token);
            $stmt->execute();
        } catch (Exception $e){
            error_log("Error: " . $e->getMessage());
        }

        if($stmt == false){
            die;
        } else {
            error_log("Word you're logged in as user: " . $_SESSION["user_id"]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $user = $row["username"];

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
        }
    } else {
        echo "You suck";
        die;
    }
*/
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$user = $row["username"];

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
        </script>

    </body>
</html>
