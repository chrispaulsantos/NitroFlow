var flag = 1;
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
                drawGraph(capacity);
            });
            capacity -= 5;
            console.log(capacity);
            if(capacity == 0){capacity = 100;}
        } else {
            clearInterval(inter);
        }
    }, 3000);
});

function drawGraph(capacity) {
    if(flag == 1){
        google.charts.load('current', {'packages':['bar']});
        google.charts.setOnLoadCallback(drawChart);
    }
    var chart = new google.charts.Bar(document.getElementById('columnchart_material'));

    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ['Location', 'Current Capacity'],
        ['200 Seaport Boulevard', capacity],
        ['200 Seaport Boulevnard', 50],
      ]);

      var options = {
        chart: {
          title: 'Flow Data',
          subtitle: 'Nitro Brews Flow Data',
        }
      };


      if(flag == 1){
          chart.draw(data, options);
          flag = 0;
      } else {
          chart.draw();
      }


    }
}
