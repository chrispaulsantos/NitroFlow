$(document).ready(function() {

    var data = {
        labels: [],
        datasets: [
            {
                label: "Keg Capacity",
                backgroundColor: "rgba(49,249,253,0.7)",
                borderColor: "rgba(49,249,253,1)",
                borderWidth: 1,
                hoverBackgroundColor: "rgba(39,199,202,0.4)",
                hoverBorderColor: "rgba(39,199,202,1)",
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
    var myBarChart = null;
    
    ids = [1,2,3,4,5];
    
    getByLocation(data, options, myBarChart);
    
});

function build_Data(data, obj){
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

function getByLocation(data, options, myBarChart){
    setInterval(function(){
        $.ajax({
            url: "src/php/getByLocation.php",
            type: "GET",
            data: {
                ids: ids
            },
            dataType: "text"
        }).done(function(response) {
            var obj = JSON.parse(response);

            build_Data(data, obj);

            $("#time").empty().append("Last Updated: " + timeStamp());
            $('#alert').empty();

            for(i = 0; i < obj.length; i++){
                if(obj[i]["current_capacity"] < 10){
                    $("#alert").append("Alert: " + obj[i]["location"] + " has less than 10% remaining." + "</br>");
                }
            }

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
    }, 2000);
}