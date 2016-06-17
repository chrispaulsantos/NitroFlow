$(document).ready(function(){
    var capacity1 = 100;
    var capacity2 = 100;
    var capacity3 = 100;
    var capacity4 = 100;
    var capacity5 = 100;

    args = [];

    args[0] = {"capacity":capacity1, "id":1};
    args[1] = {"capacity":capacity2, "id":2};
    args[2] = {"capacity":capacity3, "id":3};
    args[3] = {"capacity":capacity4, "id":4};
    args[4] = {"capacity":capacity5, "id":5};

    var inter = setInterval(function(){

        if(capacity1 >= 0){
            $.ajax({
                url: "src/php/insert_data.php",
                type: "GET",
                data: {
                    args: args
                },
                datatype: "text"
            }).done(function() {

                args[0] = {"capacity":capacity1, "id":1};
                args[1] = {"capacity":capacity2, "id":2};
                args[2] = {"capacity":capacity3, "id":3};
                args[3] = {"capacity":capacity4, "id":4};
                args[4] = {"capacity":capacity5, "id":5};

            });

            capacity1 = capacity1 - 4*Math.abs(Math.sin(Math.random(1,100)));
            capacity2 = capacity2 - 2*Math.abs(Math.sin(Math.random(1,100)));
            capacity3 = capacity3 - 3*Math.abs(Math.sin(Math.random(1,100)));
            capacity4 = capacity4 - .5*Math.abs(Math.sin(Math.random(1,100)));
            capacity5 = capacity5 - 1.5*Math.abs(Math.sin(Math.random(1,100)));
        }

        if(capacity1 < 0){capacity1 = 100;}
        if(capacity2 < 0){capacity2 = 100;}
        if(capacity3 < 0){capacity3 = 100;}
        if(capacity4 < 0){capacity4 = 100;}
        if(capacity5 < 0){capacity5 = 100;}

    }, 2000);
});