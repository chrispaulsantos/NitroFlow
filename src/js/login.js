/**
 * Created by chris on 7/29/16.
 */
$(document).ready(function() {
    var username = null;
    var password = null;

// Get username and password and verify against database
    // On login button click
    $(document).on("click","#login_button",function () {
        username = $("input[name=username]").val();
        password = $("input[name=password]").val();

        verifyUser(username, password);
    });
    // On enter key press
    $(document).on("keypress", function (e) {
        if(e.which == 13){
            username = $("input[name=username]").val();
            password = $("input[name=password]").val();

            console.log(username + " : " + password);

            verifyUser(username, password);
        }
    });
})

function verifyUser(username, password){
    $.ajax({
        type: "GET",
        url: "src/php/validate_login.php",
        data: {
            username: username,
            password: password
        },
        dataType: "text"
    }).done(function(response){
        if(response == "REDIRECT"){
            window.location = "index.php";
        } else if(response == "INCORRECT"){
            $("#wrong").removeClass("hidden");
        }
    });
}