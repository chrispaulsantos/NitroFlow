// Interval variable
var int = null;
// Global chart value
var chart = null;
// Line data for line graph
var lineData = {
    labels: [],
    datasets: []
};

$(document).ready(function() {
    var flag = false;

    // Listen on region change
    $(document).on("change","#region",function(){
        var color = randomColorGenerate(.7);

        // Variables for drawing the chart; datasets.data and labels initially empty
        var barData = {
            labels: [],
            datasets: [
                {
                    label: $("#region").val(),
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
                    ticks: {
                        max: 100,
                        min: 0,
                        stepSize: 10
                    }
                }]
            }
        };
        getByRegion(barData, options);
    });
    // Listen on location change
    $(document).on("change","#location",function(){
        // variables for drawing the chart; datasets.data and labels initially empty
        if($("#fromDate").val() == null || $("#fromDate").val() == "" || $("#toDate").val() == null || $("#toDate").val() == ""){
            $(".ui.message").removeClass("hidden");
        } else {
            var options = {
                scales: {
                    yAxes: [{
                        ticks: {
                            max: 105,
                            min: 0,
                            stepSize: 5
                        }
                    }]
                }
            };
            getByLocation(lineData, options);
        }
    });
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
    // On refresh click
    $(document).on("click",".icon.refresh",function(){
        // Check to make sure dates are selected
        if (flag != false) {
            if($("#fromDate").val() == null || $("#fromDate").val() == "" || $("#toDate").val() == null || $("#toDate").val() == "") {
                //$(".ui.message").removeClass("hidden");
                $(".icon.refresh").popup('toggle');
            }
            if($("#location").val() == null || $("#location").val() == ""){
                $(".icon.refresh").popup('toggle');
            }
        } else {
            var options = {
                scales: {
                    yAxes: [{
                        ticks: {
                            max: 105,
                            min: 0,
                            stepSize: 5
                        }
                    }]
                }
            };
            getByLocation(options);
        }
    });
});

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
function getByLocation(options){
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
                type: 'bar',
                data: lineData,
                options: options
            });
        } else {
            chart.update();
        }
    });
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

    // Set labels
    for(j = 0; j < obj[0].capacity.length; j++){
        lineData.labels[j] = " ";
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
function timeStamp() {
// Create a date object with the current time
    var now = new Date();

// Create an array with the current month, day and time
    var date = [ now.getMonth() + 1, now.getDate(), now.getFullYear() ];

// Create an array with the current hour, minute and second
    var time = [ now.getHours(), now.getMinutes(), now.getSeconds() ];

// Determine AM or PM suffix based on the hour
    var suffix = ( time[0] < 12 ) ? "AM" : "PM";

// Convert hour from military time
    time[0] = ( time[0] < 12 ) ? time[0] : time[0] - 12;

// If hour is 0, set it to 12
    time[0] = time[0] || 12;

// If seconds and minutes are less than 10, add a zero
    for ( var i = 1; i < 3; i++ ) {
        if ( time[i] < 10 ) {
            time[i] = "0" + time[i];
        }
    }

// Return the formatted string
    return date.join("/") + " " + time.join(":") + " " + suffix;
}
function updateTime(){
    $("#time").empty().append("<i class='icon refresh'></i> Last Updated: " + timeStamp());
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

// Dataset structure class
function lineStruct(data,label){
    var color = randomColorGenerate(1);

    this.label = label;
    this.fill = false;
    this.lineTension = 0.5;
    this.backgroundColor = color;
    this.borderColor = color;
    this.borderCapStyle = 'butt';
    this.borderDash = [];
    this.borderDashOffset = 0.0;
    this.borderJoinStyle = 'miter';
    this.pointBorderColor = color;
    this.pointBackgroundColor = "#fff";
    this.pointBorderWidth = 1;
    this.pointHoverRadius = 5;
    this.pointHoverBackgroundColor = color;
    this.pointHoverBorderColor = "rgba(220,220,220,1)";
    this.pointHoverBorderWidth = 2;
    this.pointRadius = 1;
    this.pointHitRadius = 10;
    this.data = data;
    this.spanGaps = true;
}