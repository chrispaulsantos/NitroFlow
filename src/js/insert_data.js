$(document).ready(function(){
    var capacity = 100;

    var inter = setInterval(function(){
        if(capacity >= 0){
            $.ajax({
                url: "src/php/insert_data.php",
                type: "GET",
                data: {
                    capacity: capacity
                },
                datatype: "text"
            }).done(function(response) {
                console.log(response);
                data.datasets[0].data[0] = capacity;
                data.datasets[0].data[5] = capacity-Math.abs((Math.floor((Math.random() * 15) + 1)));
                data.datasets[0].data[10] = capacity-Math.abs((Math.floor((Math.random() * 17) + 1)));
                myBarChart.update();
                //drawGraph(capacity);
            });
            capacity -= 1;
            console.log(capacity);
            if(capacity == 0){capacity = 100;}
        } else {
            clearInterval(inter);
        }
    }, 2000);

    var data = {
        labels: ["200 Seaport", "245 Summer", " 1 Congress", "345 State", "2 Quincy Market","200 Seaport", "245 Summer", " 1 Congress", "345 State", "2 Quincy Market",
                 "200 Seaport", "245 Summer", " 1 Congress", "345 State", "2 Quincy Market","200 Seaport", "245 Summer", " 1 Congress", "345 State", "2 Quincy Market"],
        datasets: [
            {
                label: "My First dataset",
                backgroundColor: "rgba(0,0,0,0.7)",
                borderColor: "rgba(255,99,132,1)",
                borderWidth: 1,
                hoverBackgroundColor: "rgba(255,99,132,0.4)",
                hoverBorderColor: "rgba(255,99,132,1)",
                data: [capacity, 50, 33, 74, 55,capacity, 50, 33, 74, 55,capacity, 50, 33, 74, 55,capacity, 50, 33, 74, 55],
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

    var ctx = document.getElementById("chart");

    var myBarChart = new Chart(ctx, {
        type: 'bar',
        data: data,
        options: options
    });

});
