<!DOCTYPE html>
<HTML>
    <head>
        <link rel='stylesheet' href='src/css/Semantic/semantic.min.css' type='text/css'/>

        <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
        <script src="src/css/Semantic/semantic.min.js" type="text/javascript"></script>

        <title> Nitro Flow </title>
    </head>
    <body>
        <div class="ui container center aligned" style="width:300px;">
            <div class="ui fluid middle aligned card">
                <div class="content">
                    <form class="ui left aligned form">
                        <div class="field">
                            <label>User Name</label>
                            <input name="username" placeholder="User Name" type="text">
                        </div>
                        <div class="field">
                            <label>Password</label>
                            <input name="password" placeholder="Password" type="password">
                        </div>
                        <button id="login_button" class="ui button" type="button">Login</button>
                    </form>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                height = window.innerHeight;
                $('.ui.container').css("margin-top", height/2-(230/2));
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
                    url: "src/php/validateLogin.php",
                    data: {
                        username: username,
                        password: password
                    },
                    dataType: "text"
                }).done(function(response){
                    if(response == "SUCCESS"){
                        window.location = "index.php";
                    } else if(response == "FAILURE"){
                        // TEDDY ADD JQUERY HERE
                    }
                });
            }

        </script>
    </body>
</HTML>
