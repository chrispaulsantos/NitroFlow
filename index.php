<?php
    require_once "src/php/database_connect.php";

    $regions = array();

    try {
        $stmt = DBConnection::instance()->prepare("SELECT DISTINCT region FROM Locations");
        $stmt->execute();
    } catch (Exception $e){
        error_log("Error: " .$e->getMessage());
    }

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $regions = $row["region"];
    }
    error_log(json_encode($regions));
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
        <div id="content" class="ui container" style="height: 500px; width: 900px;">
            <div class="ui right aligned container">
                <canvas id="chart"></canvas>
            </div>
            <div id="time">Last Updated: </div>
            <div id="alert" style="color: #ff0000;">Alert: </div>
        </div>
        <div>
            <select class="ui search dropdown">
                <option value="">Select Region</option>
                <option value="ALL" selected>Select All Regions</option>
                <?php foreach($regions as $region): ?>
                    <option value="'<?php echo $region; ?>'"><?php echo $region; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <script>
            $('.ui.container').css("margin-top", window.innerHeight/2-(600/2));
            //$('#chart').attr("width", window.innerWidth*.7, "height", $('#content').height()*.6);
            $('.ui.dropdown').dropdown();
        </script>
    </body>
</html>
