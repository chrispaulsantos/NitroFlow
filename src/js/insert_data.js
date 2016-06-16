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
    }, 5000);

    

});
