$(document).ready(function() {
    
    ids = [];
    ids.push(1);
    ids.push(2);
    ids.push(3);
    
    $.ajax({
        url: "src/php/get_data.php",
        type: "GET",
        data: {
            ids: ids
        },
        dataType: "text"
    }).done(function(response) {
        console.log(var obj = JSON.parse(response));
    });
});