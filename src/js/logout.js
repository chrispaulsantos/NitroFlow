/**
 * Created by chris on 7/29/16.
 */
$(document).ready(function() {
    $(document).on("click","#logout",function() {
        $.ajax({
            type: "GET",
            url: "src/php/logout.php"
        }).done(function(response) {

            if(response == "SUCCESS"){
                window.location = "../login.php";
            } else {
                console.log("Logout failed");
            }
        });
    });
})