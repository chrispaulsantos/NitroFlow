// Interval variable
var int = null;
// Global chart value
var chart = null;
// Line data for line graph
var lineData = {

    labels: [],
    datasets: []
};
//Flag used for checking which graph type is being used
var flag = false;

$(document).ready(function() {
    //Initialize all css style and jquery handlers that require the DOM to be built
    init();

    // Hide/Show inputs based on position of toggle
    $(document).on("change","input[name=graph-type]",function(){
        flag = !flag;
        $("#location-holder").toggle();
        $("#region-holder").toggle();
        $("#dates").toggle();
        // If first page load, change the select message
        if(chart == null){
            $("#location-alert").toggle();
            $("#region-alert").toggle();
        }
    });
    // Listen on region change
    $(document).on("change","#region",function(){
        var color = randomColorGenerate(.7);

        // Variables for drawing the chart; datasets.data and labels initially empty
        var barData = {
            labels: [],
            datasets: [
                {
                    label: getRegionLabel(),
                    backgroundColor: color,
                    borderColor: color,
                    borderWidth: 1,
                    hoverBackgroundColor: color,
                    hoverBorderColor: color,
                    data: [],
                }
            ]
        };
        var options = {
            scales: {
                yAxes: [{
                    gridLines: {
                        display: false
                    },
                    ticks: {
                        max: 100,
                        min: 0,
                        stepSize: 10
                    }
                }],
                xAxes: [{
                    gridLines: {
                        display: false
                    }
                }]
            }
        };
        getByRegion(barData, options);
    });
    // Listen on location change
    $(document).on("change","#location",function(){
        updateLocation();
    });
    // On refresh click
    $(document).on("click",".icon.refresh",function(){
        updateLocation();
    });
    // On add account menu click
    $(document).on("click","#addAccMenu",function(){
        $("#addAccDim").dimmer("show");
    })
    // On add account button order click
    $(document).on("click","#addAccSubmit",function(){
        $(this).addClass("loading");
        $.ajax({
            url: "src/php/addAccount.php",
            data: {
                acc: parseAddAcct()
            }
        }).done(function(response){
            if(response != "EXISTS"){
                $("#addAccSubmit").removeClass("loading").addClass("positive").empty().append("Account Created");
            } else {
                $("#addAccSubmit").removeClass("loading").addClass("negative").empty().append("Account Exists");
            }
            setTimeout(function(){
                $("#addAccSubmit").removeClass("negative positive").empty().append("Add Account");
            },2000);
        });
    })
    // On add account close
    $(document).on("click","#closeAddAcc", function(){
        $("#addAccDim").dimmer("hide");
    })
    // On request units menu click
    $(document).on("click","#reqUnitMenu",function(){
        $("#reqUnitDim").dimmer("show").addClass("active");
    })
    // On request units button click
    $(document).on("click","#reqUnitsSubmit",function(){
        $(this).addClass("loading");
        $.ajax({
             url: "src/php/requestUnits.php",
             data: {
                acc: parseReqUnits()
             }
        }).done(function(response){
            console.log(response);
            if(response == true){
                $("#reqUnitsSubmit").removeClass("loading").addClass("positive").empty().append("Account Updated");
            } else {
                $("#reqUnitsSubmit").removeClass("loading").addClass("negative").empty().append("Error");
            }
            setTimeout(function(){
                $("#reqUnitsSubmit").removeClass("negative positive").empty().append("Request Units");
            },2000);
        });
    })
    // On request units close
    $(document).on("click","#closeReqUnit", function(){
        $("#reqUnitDim").dimmer("hide");
    })
    // On logout click
    $(document).on("click","#logout",function() {
        $.ajax({
            type: "GET",
            url: "src/php/logout.php"
        }).done(function(response) {
            if(response == "SUCCESS"){
                window.location = "login.php";
            } else {
                console.log("Logout failed");
            }
        });
    });
});

function init(){
    $('#addAccForm').css("margin-top", window.innerHeight/2-(150));
    $('#reqUnitForm').css("margin-top", window.innerHeight/2-(150));
    $('.ui.dropdown').dropdown({ fullTextSearch: true });
    $( "#fromDate" ).datepicker();
    $( "#toDate" ).datepicker();
    $('.message .close').on('click', function() {
        $(this).closest('.message').transition('fade');
    });
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
    var loginInt = setInterval(function(){
        console.log("Session checked.");

        $.ajax({
            url: "src/php/loginCheck.php",
            dataType: "text"
        }).done(function(response){
            if(response == "FAIL"){
                window.location = "login.php";
            }
        });
    },60000);
}
function getRegionLabel(){
    label = $("#region").val();
    if(label == "ALL"){
        return "All Regions";
    } else {
        return label;
    }
}
function getByRegion(data, options){
    var region = $("#region").val();
    $("#chartHolder").empty().append("<canvas id='chart' width='400' height='250'></canvas>");

    // Draw graph initially on pageload
    var ctx = document.getElementById("chart");
    chart = new Chart(ctx, {
        type: 'bar',
        data: data,
        options: options
    });

    // Update data every x seconds
    if(int != null){
        clearInterval(int);
    }
    int = setInterval(function(){
        $.ajax({
            url: "src/php/getByRegion.php",
            type: "GET",
            data: {
                region: region
            },
            dataType: "text"
        }).done(function(response) {

            var obj = JSON.parse(response);
            // Build the data from the php response
            buildBarData(data, JSON.parse(response));

            // Update the current time, and empty the alerts div
            updateTime();

            // Set alerts, if any less than defined amount
            for(i = 0; i < obj.length; i++){
                if(obj[i]["current_capacity"] < 30){
                    $("#alert").append("<div id='alert' class='ui segment' style='color: rgba(211,47,47 ,1);'>" +
                                           " Alert: " + obj[i]['location'] +
                                       "</div>");
                }
            }

            // If chart is null, draw, else, update
            if(chart == null){
                var ctx = document.getElementById("chart");
                chart = new Chart(ctx, {
                    type: 'bar',
                    data: data,
                    options: options
                });
            } else {
                chart.update();
            }
        });
    }, 5000);
}
function updateLocation(){
    var dateCheck = false;
    var locationCheck = false;
    // variables for drawing the chart; datasets.data and labels initially empty
    if (flag != false) {
        if($("#fromDate").val() == null || $("#fromDate").val() == "" || $("#toDate").val() == null || $("#toDate").val() == "") {
            dateCheck = false;
            var selector = $('#dates');
            selector.popup({
                target: selector,
                content: "Please select a valid date range!",
                closable: false
            });
            selector.popup('show');
        } else {
            $("#dates").popup('destroy');
            dateCheck = true;
        }
        if($("#location").val() == null || $("#location").val() == ""){
            locationCheck = false;
        } else {
            locationCheck = true;
        }

        if(dateCheck && locationCheck){
            //
            var options = {
                scales: {
                    yAxes: [{
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            max: 105,
                            min: 0
                        },
                        afterBuildTicks: function(chart) {
                            chart.ticks = [];
                            for(var i = 0; i <= 100; i += 10){
                                chart.ticks.push(i);
                            }

                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            display: false
                        }
                    }]
                }
            };
            // Have to clear interval if graph is changed to line after region
            if(int != null){
                clearInterval(int);
            }

            ids = $("#location").val();
            $("#chartHolder").empty().append("<canvas id='chart' width='400' height='250'></canvas>");

            // Draw graph initially on pageload
            var ctx = document.getElementById("chart");
            chart = new Chart(ctx, {
                type: 'line',
                data: lineData,
                options: options
            });

            // Get date values
            var fromDate = $("#fromDate").val();
            var toDate = $("#toDate").val();

            $.ajax({
                url: "src/php/getByLocation.php",
                type: "GET",
                data: {
                    ids: ids,
                    fromDate: fromDate,
                    toDate: toDate
                },
                dataType: "text"
            }).done(function(response) {
                console.log(JSON.parse(response));
                // Build the data from the php response
                buildLineData(JSON.parse(response));

                // Update the current time, and empty the alerts div
                updateTime();

                // If chart is null, draw, else, update
                if(chart == null){
                    var ctx = document.getElementById("chart");
                    chart = new Chart(ctx, {
                        type: 'line',
                        data: lineData,
                        options: options
                    });
                } else {
                    chart.update();
                }
            });
        }
    }
}
function buildBarData(data, obj){

// For each object in return value, set datasets equal to capacity and labels equal to location
    for(i = 0; i < obj.length; i++){
        data.datasets[0].data[i] = obj[i]["current_capacity"];
        data.labels[i] = obj[i]["location"];
    }
    return data;
}
function buildLineData(obj){

    var numDatasets = $("#location").val().length;
    lineData.datasets = [];
    lineData.labels = [];

    console.log(obj);
    // Set labels
    for(i = 0; i < obj[0].capacity.length; i++){
        lineData.labels[i] = moment(obj[0].timeStamp[i]*1000).format("M/D/YY HH:mm");
    }

    // For each object in return value, set datasets equal to capacity
    for(i = 0; i < numDatasets; i++) {
        var label = obj[i].location;
        var cap = [];

        // Push data into cap placeholder array
        for (j = 0; j < obj[i].capacity.length; j++) {
            cap[j] = obj[i].capacity[j];
        }

        // Push data into lineData
        lineData.datasets[i] = new lineStruct(cap,label);
    }

    return lineData;
}
function updateTime(){
    $("#time").empty().append("<i class='icon refresh' style='cursor: pointer'></i> Last Updated: " + moment().format("MMM DD YYYY HH:mm:ss A"));
    $('#alert').empty();
}
function randomColorGenerate(alpha){
    var R = Math.floor((Math.random() * 255) + 0);
    var G = Math.floor((Math.random() * 255) + 0);
    var B = Math.floor((Math.random() * 255) + 0);

    var RGBA = "rgba(" + R.toString() + "," + G.toString() + "," + B.toString() + "," + alpha.toString() + ")";
    console.log(RGBA);
    return RGBA;
}
function parseAddAcct(){
    var obj = {
        accName: $("#acctName").val(),
        accAddress: $("#address").val(),
        accAptNum: $("#address-2").val(),
        accState: $("#state").val(),
        accZip: $("#zip").val(),
        accUnitCount: $("#unitCount").val()
    };
    return obj;
}
function parseReqUnits(){
    var obj = {
        accId: $("#reqAccName").val(),
        units: $("#reqUnitCount").val()
    }
    return obj;
}

// Dataset structure class
function lineStruct(data,label){
    var color = randomColorGenerate(1);

    this.label                     = label;
    this.fill                      = false;
    this.lineTension               = 0.5;
    this.backgroundColor           = color;
    this.borderColor               = color;
    this.borderCapStyle            = 'butt';
    this.borderDash                = [];
    this.borderDashOffset          = 0.0;
    this.borderJoinStyle           = 'miter';
    this.pointBorderColor          = color;
    this.pointBackgroundColor      = "#fff";
    this.pointBorderWidth          = 1;
    this.pointHoverRadius          = 5;
    this.pointHoverBackgroundColor = color;
    this.pointHoverBorderColor     = "rgba(220,220,220,1)";
    this.pointHoverBorderWidth     = 2;
    this.pointRadius               = 3;
    this.pointHitRadius            = 10;
    this.data                      = data;
    this.spanGaps                  = true;
}