var int = null;
var chart = null;
var datasetStructure = {
    label: "",
    fill: false,
    lineTension: 0.7,
    backgroundColor: "rgba(75,192,192,0.4)",
    borderColor: "rgba(75,192,192,1)",
    borderCapStyle: 'butt',
    borderDash: [],
    borderDashOffset: 0.0,
    borderJoinStyle: 'miter',
    pointBorderColor: "rgba(75,192,192,1)",
    pointBackgroundColor: "#fff",
    pointBorderWidth: 1,
    pointHoverRadius: 5,
    pointHoverBackgroundColor: "rgba(75,192,192,1)",
    pointHoverBorderColor: "rgba(220,220,220,1)",
    pointHoverBorderWidth: 2,
    pointRadius: 1,
    pointHitRadius: 10,
    data: [],
    spanGaps: false,
}
var lineData = {
    labels: [],
    datasets: []
};

$(document).ready(function() {

    // Listen on region change
    $(document).on("change","#region",function(){
        // variables for drawing the chart; datasets.data and labels initially empty
        var barData = {
            labels: [],
            datasets: [
                {
                    label: "Keg Capacity",
                    backgroundColor: "rgba(13,71,161, .7)",
                    borderColor: "rgba(13,71,161, .7)",
                    borderWidth: 1,
                    hoverBackgroundColor: "rgba(13,71,161, .4)",
                    hoverBorderColor: "rgba(13,71,161, .4)",
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
        getByLocation(lineData, options);
    });
    // Hide/Show inputs based on position of toggle
    $(document).on("change","input[name=graph-type]",function(){
        $("#location-holder").toggle();
        $("#region-holder").toggle();
        $("#dates").toggle();
    });
    // On refresh
    $(document).on("click",".icon.refresh",function(){
        var lineData = {
            labels: [],
            datasets: [
                {
                    label: "My First dataset",
                    fill: false,
                    lineTension: 0.1,
                    backgroundColor: "rgba(75,192,192,0.4)",
                    borderColor: "rgba(75,192,192,1)",
                    borderCapStyle: 'butt',
                    borderDash: [],
                    borderDashOffset: 0.0,
                    borderJoinStyle: 'miter',
                    pointBorderColor: "rgba(75,192,192,1)",
                    pointBackgroundColor: "#fff",
                    pointBorderWidth: 1,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "rgba(75,192,192,1)",
                    pointHoverBorderColor: "rgba(220,220,220,1)",
                    pointHoverBorderWidth: 2,
                    pointRadius: 1,
                    pointHitRadius: 10,
                    data: [65, 59, 80, 81, 56, 55, 40],
                    spanGaps: false,
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
        getByLocation(lineData, options);
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
                data: data,
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
    //lineData.datasets[0].label = $("#location").val();
    var numDatasets = $("#location").val().length;

    // Set labels
    for(j = 0; j < obj[0].capacity.length; j++){
        lineData.labels[j] = " ";
    }
    //console.log(data);
    // For each object in return value, set datasets equal to capacity
    for(i = 0; i < numDatasets; i++) {
        datasetStructure.data = [];
        datasetStructure.label = obj[i].location;
        for (j = 0; j < obj[i].capacity.length; j++) {
            datasetStructure.data[j] = obj[i].capacity[j];
        }
        lineData.datasets[i] = datasetStructure;
    }

    console.log(lineData);
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
