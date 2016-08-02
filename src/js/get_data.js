$(document).ready(function() {
    var myBarChart = null;
    ids = [1,2,3,4,5];

    // Locations for search box
    getLocations();

    $(document).on("change","#region",function(){
        // variables for drawing the chart; datasets.data and labels initially empty
        /*var barData = {
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
        getByRegion(barData, options, myBarChart);*/
        console.log("Change successful");
    });
    $(document).on("click",".search.link.icon",function(){
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
        getByRegion(barData, options, myBarChart);
        console.log("Click successful");
    });
});

function build_Data(data, obj){

// For each object in return value, set datasets equal to capacity and labels equal to location
    for(i = 0; i < obj.length; i++){
        data.datasets[0].data[i] = obj[i]["current_capacity"];
        data.labels[i] = obj[i]["location"];
    }
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

function getByRegion(data, options, myBarChart){

// Draw graph initially on pageload
    var ctx = document.getElementById("chart");
    myBarChart = new Chart(ctx, {
        type: 'bar',
        data: data,
        options: options
    });

// Update data every x seconds
    setInterval(function(){
        $.ajax({
            url: "src/php/getByLocation.php",
            type: "GET",
            data: {
                ids: ids
            },
            dataType: "text"
        }).done(function(response) {

        // Build the data from the php response
            var obj = JSON.parse(response);
            build_Data(data, obj);

        // Update the current time, and empty the alerts div
            $("#time").empty().append("Last Updated: " + timeStamp());
            $('#alert').empty();

        // Set alerts, if any less than defined amount
            for(i = 0; i < obj.length; i++){
                if(obj[i]["current_capacity"] < 30){
                    $("#alert").append("<div id='alert' class='ui segment' style='color: rgba(211,47,47 ,1);'>" +
                                           " Alert: " + obj[i]['location'] +
                                       "</div>");
                }
            }

        // If chart is null, draw, else, update
            if(myBarChart == null){
                var ctx = document.getElementById("chart");
                myBarChart = new Chart(ctx, {
                    type: 'bar',
                    data: data,
                    options: options
                });
            } else {
                myBarChart.update();
            }
        });
    }, 5000);
}

function getLocations(){
    $.ajax({
        type: "GET",
        url: "src/php/getLocations.php",
        dataType: "text"
    }).done(function(response){
        var locations = JSON.parse(response);
        console.log(locations);

        $('.ui.search').search({
            source : locations,
            searchFields : [
                'title'
            ],
        });
    });
}