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
        <link rel="stylesheet" href="src/includes/jquery-ui-1.12.0/jquery-ui.min.css"/>
        <link rel="stylesheet" href="src/includes/noUiSlider/nouislider.min.css"/>

        <script src="src/includes/jquery-1.12.4.min.js"></script>
        <script src="src/includes/jquery-ui-1.12.0/jquery-ui.js"></script>
        <script src="src/js/Chart.js" type="text/javascript"></script>
        <script src="src/js/get_data.js" type="text/javascript"></script>
        <script src="src/css/Semantic/semantic.min.js" type="text/javascript"></script>
        <script src="src/js/logout.js" type="text/javascript"></script>
        <script src="src/includes/noUiSlider/nouislider.min.js" type="text/javascript"></script>
        <script src="src/includes/wNumb.js" type="text/javascript"></script>
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


                        <div id="slider-range"></div>
                        <div id="event-start"></div>
                        <div id="event-end"></div>

                    </div>
                    <div class="ui slider checkbox">
                        <input name="graph-type" type="checkbox">
                        <label><i class="icon line chart"></i>By Location</label>
                    </div>
                    <div class="ui small fluid left aligned segment input" id="dates">
                        <input type="text" id="fromDate" placeholder="From Date">
                        <input type="text" id="toDate" placeholder="To Date">
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


            var dateSlider = document.getElementById('slider-range');

            noUiSlider.create(dateSlider, {
            // Create two timestamps to define a range.
                range: {
                    min: timestamp('2010'),
                    max: timestamp('2016')
                },

                // Steps of one week
                step: 7 * 24 * 60 * 60 * 1000,

                // Two more timestamps indicate the handle starting positions.
                start: [ timestamp('2011'), timestamp('2015') ],

                // No decimals
                format: wNumb({
                    decimals: 0
                })
            });

            var dateValues = [
                document.getElementById('event-start'),
                document.getElementById('event-end')
            ];

            dateSlider.noUiSlider.on('update', function( values, handle ) {
                dateValues[handle].innerHTML = formatDate(new Date(+values[handle]));
            });
            // Create a list of day and monthnames.
            var
                weekdays = [
                    "Sunday", "Monday", "Tuesday",
                    "Wednesday", "Thursday", "Friday",
                    "Saturday"
                ],
                months = [
                    "January", "February", "March",
                    "April", "May", "June", "July",
                    "August", "September", "October",
                    "November", "December"
                ];

            // Append a suffix to dates.
            // Example: 23 => 23rd, 1 => 1st.
            function nth (d) {
                if(d>3 && d<21) return 'th';
                switch (d % 10) {
                    case 1:  return "st";
                    case 2:  return "nd";
                    case 3:  return "rd";
                    default: return "th";
                }
            }

            // Create a string representation of the date.
            function formatDate ( date ) {
                return weekdays[date.getDay()] + ", " +
                    date.getDate() + nth(date.getDate()) + " " +
                    months[date.getMonth()] + " " +
                    date.getFullYear();
            }

            function timestamp(str){
                return new Date(str).getTime();
            }

        </script>

    </body>
</html>
