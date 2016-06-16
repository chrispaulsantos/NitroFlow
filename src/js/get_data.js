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
    
    ids = [];
    ids.push(1);
    ids.push(2);
    ids.push(3);
    ids.push(4);
    
    setInterval(function(){
        $.ajax({
            url: "src/php/get_data.php",
            type: "GET",
            data: {
                ids: ids
            },
            dataType: "text"
        }).done(function(response) {
            var obj = JSON.parse(response);
            build_Data(data, obj);
            
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
    },5000);
    
});

function build_Data(data, obj){
    for(i = 0; i < obj.length; i++){
        data.datasets[0].data[i] = obj[i]["current_capacity"];
        data.labels[i] = obj[i]["location"];
    }
}