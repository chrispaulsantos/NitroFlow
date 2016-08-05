<?php
    require_once "src/php/database_connect.php";
    session_start();

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
        //header("Location: http://127.0.0.1:8888/login.php"); /* Redirect browser */
        exit();
    }

    // initialize region and location arrays
    $regions = array();
    $locations = array();

    // Get regions
    try {
        $stmt = DBConnection::instance()->prepare("SELECT DISTINCT region FROM Locations");
        $stmt->execute();
    } catch (Exception $e){
        error_log("Error: " .$e->getMessage());
    }
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $regions[] = $row["region"];
    }
    // Get locations
    try {
        $stmt = DBConnection::instance()->prepare("SELECT P_Id, location FROM Locations");
        $stmt->execute();
    } catch (Exception $e){
        error_log("Error: " .$e->getMessage());
    }
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $locations[] = ['id' => $row["P_Id"], 'location' => $row["location"]];
    }
?>

<html>
    <head>
        <link rel='stylesheet' href='src/css/Semantic/semantic.min.css' type='text/css'/>
        <link rel="stylesheet" href="src/includes/jquery-ui-1.12.0/jquery-ui.min.css">

        <script src="src/includes/jquery-1.12.4.min.js"></script>
        <script src="src/includes/jquery-ui-1.12.0/jquery-ui.js"></script>
        <script src="src/js/Chart.js" type="text/javascript"></script>
        <script src="src/js/get_data.js" type="text/javascript"></script>
        <script src="src/css/Semantic/semantic.min.js" type="text/javascript"></script>
        <script src="src/js/logout.js" type="text/javascript"></script>
    </head>
    <body>

        <div class="ui menu">
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

        <div id='content' class="ui container" style="height: 500px; width: 1100px;">
            <div class="ui grid">

                <div class="five wide column">
                    <div class="ui left aligned segment">
                        <div id="region-holder">
                            <select id="region" class="ui fluid scrolling search dropdown">
                                <option value="">Select Region</option>
                                <option value="ALL">Select All Regions</option>
                                <?php foreach($regions as $region): ?>
                                    <option value="<?php echo $region; ?>"><?php echo $region; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="location-holder">
                            <select id="location" class="ui fluid scrolling search dropdown" multiple="">
                                <option value="">Select Location</option>
                                <option value="ALL">Select All Locations</option>
                                <?php foreach($locations as $location): ?>
                                    <option value="<?php echo $location['id']; ?>"><?php echo $location['location']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <style>
                            #slider-range,#SlideMax{width:400px;}
                            #slider-range,#time, #SlideMax, table{margin:10px;display:block;}
                        </style>
                        <div id="slider-range"></div>
                        <span id="SlideMax"></span>
                        <br />
                        <span id="time-range"></span>
                        <input type="submit" name="scheduleSubmit" value="Submit" id="scheduleSubmit" class="ui-button ui-state-default ui-corner-all"/>
                        <table id="Schedule">
                            <thead>
                            <tr>
                                <th>Start Time</th>
                                <th>End Time</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>

                    </div>
                    <div class="ui slider checkbox">
                        <input name="graph-type" type="checkbox">
                        <label><i class="icon line chart"></i>By Location</label>
                    </div>
                    <div class="ui small fluid left aligned segment input" id="dates">
                        <input type="text" id="fromDate" placeholder="From Date">
                        <input type="text" id="toDate" placeholder="To Date">
                        <div class="ui hidden warning message">
                            <i class="close icon"></i>
                            <div class="header">
                                Please enter a date range!
                            </div>
                        </div>
                    </div>
                    <div id="alert" class="ui scrollable left aligned segment"></div>

                </div>
                <div class="eleven wide column">
                    <div class="ui right left segment">
                        <div id="chartHolder" class="ui segment">
                            <div id="region-alert">
                                <i class="icon angle left"></i>Please select a region.
                            </div>
                            <div id="location-alert">
                                <i class="icon angle left"></i>Please select a location.
                            </div>
                        </div>
                        <div id="time"><i class="icon refresh"></i> Last Updated: </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $('#content').css("margin-top", window.innerHeight/2-(300));
            $('.ui.dropdown').dropdown({ fullTextSearch: true });
            $( "#fromDate" ).datepicker();
            $( "#toDate" ).datepicker();
            $('.message .close').on('click', function() {
                    $(this).closest('.message').transition('fade');
            });
            $(document).ready(function(){
                $("#location-holder").hide();
                $("#dates").hide();
                $("#location-alert").hide();
            });


            var startTime;
            var endTime;
            $("#slider-range").slider({
                range: true, min: 0, max: 1439, values: [540, 1020], step:15, slide: slideTime, change: checkMax
            });
            function slideTime(event, ui){
                var val0 = $("#slider-range").slider("values", 0),
                    val1 = $("#slider-range").slider("values", 1),
                    minutes0 = parseInt(val0 % 60, 10),
                    hours0 = parseInt(val0 / 60 % 24, 10),
                    minutes1 = parseInt(val1 % 60, 10),
                    hours1 = parseInt(val1 / 60 % 24, 10);

                startTime = getTime(hours0, minutes0);
                endTime = getTime(hours1, minutes1);
                $("#time-range").text(startTime + ' - ' + endTime);
            }
            function getTime(hours, minutes) {
                var time = null;
                minutes = minutes + "";
                if (hours < 12) {
                    time = "AM";
                }
                else {
                    time = "PM";
                }
                if (hours == 0) {
                    hours = 12;
                }
                if (hours > 12) {
                    hours = hours - 12;
                }
                if (minutes.length == 1) {
                    minutes = "0" + minutes;
                }
                return hours + ":" + minutes + " " + time;
            }
            function checkMax() {
                var size = $("#slider-range").slider("values", 1) - $("#slider-range").slider("values", 0);
                if( size >= 1435) {
                    $("#slider-range div")
                        .addClass("ui-state-error")
                        .removeClass("ui-widget-header");
                    $("#scheduleSubmit")
                        .attr("disabled","disabled")
                        .addClass("ui-state-disabled")
                        .removeClass("ui-state-default");
                    $("#SlideMax").text("Cannot be more than 24 hours");
                }
                else {
                    $("#slider-range div")
                        .addClass("ui-widget-header")
                        .removeClass("ui-state-error");
                    $("#scheduleSubmit")
                        .removeAttr("disabled")
                        .addClass("ui-state-default")
                        .removeClass("ui-state-disabled");
                    $("#SlideMax").text("");
                }
            }

            $("#scheduleSubmit").on('click', function(){
                console.log(startTime);
                console.log(endTime);
                $('#Schedule tbody').append('<tr>' +
                    '<td>' + startTime + '</td>' +
                    '<td>' + endTime + '</td>' +
                    '</tr>');
            });
            slideTime();
        </script>

    </body>
</html>
