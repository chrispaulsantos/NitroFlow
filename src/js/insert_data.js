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
            });
            capacity -= 5;
            console.log(capacity);
        } else {
            clearInterval(inter);
        }
    }, 10000);
})
