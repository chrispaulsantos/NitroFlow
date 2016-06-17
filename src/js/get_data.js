$(document).ready(function() {

    var data = {
        labels: [],
        datasets: [
            {
                label: "My First dataset",
                backgroundColor: "rgba(0,0,0,0.7)",
                borderColor: "rgba(255,99,132,1)",
                borderWidth: 1,
                hoverBackgroundColor: "rgba(255,99,132,0.4)",
                hoverBorderColor: "rgba(255,99,132,1)",
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
    
    ids = [1,2,3,4];
    
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
            
            $("#time").empty().append("Last Updated: " + obj[0]["time"]);
            $('#alert').empty();

            for(i = 0; i < obj.length; i++){
                if(obj[i]["current_capacity"] < 10){
                    $("#alert").append("Alert: " + obj[i]["location"] + " has less than 10% remaining.\n");
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
    
});

function build_Data(data, obj){
    for(i = 0; i < obj.length; i++){
        data.datasets[0].data[i] = obj[i]["current_capacity"];
        data.labels[i] = obj[i]["location"];
    }
}