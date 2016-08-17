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
        exit();
    }

    // initialize region and location arrays
    $regions = array();
    $locations = array();
    $accNames = array();

    // Get regions
    try {
        $stmt = DBConnection::instance()->prepare("SELECT DISTINCT AccZip FROM Locations");
        $stmt->execute();
    } catch (Exception $e){
        error_log("Error: " .$e->getMessage());
    }
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $regions[] = $row["AccZip"];
    }
    // Get locations
    try {
        $stmt = DBConnection::instance()->prepare("SELECT P_Id, AccStrAdd FROM Locations");
        $stmt->execute();
    } catch (Exception $e){
        error_log("Error: " .$e->getMessage());
    }
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $locations[] = ['id' => $row["P_Id"], 'location' => $row["AccStrAdd"]];
    }
    // Get account names
?>

<html>
    <head>
        <link rel='stylesheet' href='src/css/Semantic/semantic.min.css' type='text/css'/>
        <link rel="stylesheet" href="src/includes/jquery-ui-1.12.0/jquery-ui.min.css"/>

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
                        <div class="item" id="addAccBt">Add Account</div>
                        <div class="item" id="reqUnitBt">Request Units</div>
                        <div class="item"><?php echo $user ?></div>
                        <div class="divider"></div>
                        <div class="item" id="logout">Logout</div>
                    </div>
                </div>
            </div>
        </div>

        <div id='content' class="ui blurring container" style="height: 500px; width: 1100px;">
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
                            </select> <!-- Region dropdown -->
                        </div>
                        <div id="location-holder">
                            <select id="location" class="ui fluid scrolling search dropdown" multiple="">
                                <option value="">Select Location</option>
                                <option value="ALL">Select All Locations</option>
                                <?php foreach($locations as $location): ?>
                                    <option value="<?php echo $location['id']; ?>"><?php echo $location['location']; ?></option>
                                <?php endforeach; ?>
                            </select> <!-- Location dropdown -->
                        </div>

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

        <div id="addAccDim" class="ui inverted page dimmer">
            <div class="ui container" style="width:500px; height:196px;">
                <form id="addAccForm" class="ui form">
                    <h4 class="ui left aligned dividing header">
                        Add Account
                        <div class="ui icon right aligned" style="float: right;margin-right: 0px;">
                            <i id="closeAddAcc" class="remove icon" style="cursor: pointer;"></i>
                        </div>
                    </h4>
                    <div class="field">
                        <div class="field">
                                <input id="acctName" placeholder="Account Name" type="text">
                            </div> <!-- Account Name Field -->
                    </div>
                    <div class="field">
                        <div class="fields">
                            <div class="twelve wide field">
                                <input id="address" placeholder="Street Address" type="text">
                            </div> <!-- Street Address Field -->
                            <div class="four wide field">
                                <input id="address-2" placeholder="Apt #" type="text">
                            </div> <!-- Street Address 2 Field -->
                        </div>
                    </div>
                    <div class="field">
                        <div class="three fields">
                            <div class="seven wide field">
                                <label></label>
                                <select id="state" class="ui fluid search dropdown">
                                    <option value="">State</option>
                                    <option value="AL">Alabama</option>
                                    <option value="AK">Alaska</option>
                                    <option value="AZ">Arizona</option>
                                    <option value="AR">Arkansas</option>
                                    <option value="CA">California</option>
                                    <option value="CO">Colorado</option>
                                    <option value="CT">Connecticut</option>
                                    <option value="DE">Delaware</option>
                                    <option value="DC">District Of Columbia</option>
                                    <option value="FL">Florida</option>
                                    <option value="GA">Georgia</option>
                                    <option value="HI">Hawaii</option>
                                    <option value="ID">Idaho</option>
                                    <option value="IL">Illinois</option>
                                    <option value="IN">Indiana</option>
                                    <option value="IA">Iowa</option>
                                    <option value="KS">Kansas</option>
                                    <option value="KY">Kentucky</option>
                                    <option value="LA">Louisiana</option>
                                    <option value="ME">Maine</option>
                                    <option value="MD">Maryland</option>
                                    <option value="MA">Massachusetts</option>
                                    <option value="MI">Michigan</option>
                                    <option value="MN">Minnesota</option>
                                    <option value="MS">Mississippi</option>
                                    <option value="MO">Missouri</option>
                                    <option value="MT">Montana</option>
                                    <option value="NE">Nebraska</option>
                                    <option value="NV">Nevada</option>
                                    <option value="NH">New Hampshire</option>
                                    <option value="NJ">New Jersey</option>
                                    <option value="NM">New Mexico</option>
                                    <option value="NY">New York</option>
                                    <option value="NC">North Carolina</option>
                                    <option value="ND">North Dakota</option>
                                    <option value="OH">Ohio</option>
                                    <option value="OK">Oklahoma</option>
                                    <option value="OR">Oregon</option>
                                    <option value="PA">Pennsylvania</option>
                                    <option value="RI">Rhode Island</option>
                                    <option value="SC">South Carolina</option>
                                    <option value="SD">South Dakota</option>
                                    <option value="TN">Tennessee</option>
                                    <option value="TX">Texas</option>
                                    <option value="UT">Utah</option>
                                    <option value="VT">Vermont</option>
                                    <option value="VA">Virginia</option>
                                    <option value="WA">Washington</option>
                                    <option value="WV">West Virginia</option>
                                    <option value="WI">Wisconsin</option>
                                    <option value="WY">Wyoming</option>
                                </select>
                            </div> <!-- State Field -->
                            <div class="five wide field">
                                <label></label>
                                <input class="ui input" id="zip" placeholder="Zip Code"></input>
                            </div> <!-- Zip Code Field -->
                            <div class="five wide field">
                                <label></label>
                                <select id="unitCount" class="ui fluid search dropdown">
                                    <option value="">Unit Count</option>
                                    <option value="1">1</option>
                                    <?php for($i = 5; $i <= 100; $i+=5){ ?>
                                    <option value="<?php echo $i ?>"><?php echo $i ?></option>
                                    <?php } ?>
                                </select>
                            </div> <!-- Unit Count Field -->
                        </div>
                    </div>
                    <div class="ui divider"></div>
                    <div id="submitOrd" class="ui left aligned button" tabindex="0">Add Account Order</div>
                </form> <!-- Add Account Form -->
            </div>
        </div>
        <div id="reqUnitDim" class="ui inverted page dimmer">
            <div class="ui container" style="width:500px; height:196px;">
                <form id="reqUnitForm" class="ui form">
                    <h4 class="ui left aligned dividing header">
                        Request Units
                        <div class="ui icon right aligned" style="float: right;margin-right: 0px;">
                            <i id="closeReqUnit" class="remove icon" style="cursor: pointer"></i>
                        </div>
                    </h4>
                    <div class="field">
                        <div class="field">
                            <label></label>
                            <select id="reqAccName" class="ui fluid search dropdown">
                                <option value="">Account</option>
                                <?php foreach($locations as $location): ?>
                                    <option value="<?php echo $location["id"] ?>"><?php echo $location["location"] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div> <!-- Account Name Dropdown Field -->
                        <div class="field">
                            <label></label>
                            <select id="reqUnitCount" class="ui fluid search dropdown">
                                <option value="">Unit Count</option>
                                <option value="1">1</option>
                                <?php for($i = 5; $i <= 100; $i+=5){ ?>
                                    <option value="<?php echo $i ?>"><?php echo $i ?></option>
                                <?php } ?>
                            </select>
                        </div> <!-- Unit Count Field -->
                    </div>
                    <div class="ui divider"></div>
                    <div id="reqUnits" class="ui left aligned button" tabindex="0">Request Units</div>
                </form> <!-- Request Unit Form -->
            </div>
        </div>

        <script>
            $('#content').css("margin-top", window.innerHeight/2-(300));
            $('#addAccForm').css("margin-top", window.innerHeight/2-(150));
            $('#reqUnitForm').css("margin-top", window.innerHeight/2-(150));
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
                $("#addAccDim").dimmer({
                    closable: false,
                    duration: {
                        show:500,
                        hide:500
                    }
                });
                $("#reqUnitDim").dimmer({
                    closable: false,
                    duration: {
                        show:500,
                        hide:500
                    }
                });
            });
        </script>

    </body>
</html>
