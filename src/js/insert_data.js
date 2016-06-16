$(document).ready(function(){
    var capacity1 = 100;
    var capacity2 = 100;
    var capacity3 = 100;
    var capacity4 = 100;

    args = [];

    args[0] = {"capacity":capacity1, "id":1};
    args[1] = {"capacity":capacity2, "id":2};
    args[2] = {"capacity":capacity3, "id":3};
    args[3] = {"capacity":capacity4, "id":4};

    var inter = setInterval(function(){
        if(capacity >= 0){
            $.ajax({
                url: "src/php/insert_data.php",
                type: "GET",
                data: {
                    args: args
                },
                datatype: "text"
            }).done(function(response) {
                console.log(response);

                args[0] = {"key":"capacity", "value":capacity1};
                args[1] = {"key":"capacity", "value":capacity2};
                args[2] = {"key":"capacity", "value":capacity3};
                args[3] = {"key":"capacity", "value":capacity4};


            });
            //console.log(capacity);

            capacity1 = capacity1 - 4*Math.abs(Math.sin(Math.random(1,100)));
            capacity2 = capacity2 - 2*Math.abs(Math.sin(Math.random(1,100)));
            capacity3 = capacity3 - 3*Math.abs(Math.sin(Math.random(1,100)));
            capacity4 = capacity4 - .5*Math.abs(Math.sin(Math.random(1,100)));
        } else {
            //clearInterval(inter);
            if(capacity1 < 0){capacity1 = 100;}
            if(capacity2 < 0){capacity2 = 100;}
            if(capacity3 < 0){capacity3 = 100;}
            if(capacity4 < 0){capacity4 = 100;}
        }

    }, 2000);
});