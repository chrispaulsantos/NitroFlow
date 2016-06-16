<html>
    <head>
        <link rel='stylesheet' href='src/css/Semantic/semantic.min.css' type='text/css'/>

        <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
        <script type="text/javascript" src="src/js/Chart.js"></script>
        <script src="src/js/get_data.js" type="text/javascript"></script>
        <script src="src/css/Semantic/semantic.min.js" type="text/javascript"></script>
    </head>
    <body>
        <div id="content" class="ui container">
            <div class="ui right aligned container">
                <canvas id="chart"></div>
            </div>
            <div class="ui left aligned container">
                <div class="ui fluid middle aligned card">
                    <div class="content">

                        <form action="" method='GET' class="ui form" style="" >

                            <div class ="four fields">
                                <div class="field">
                                    <select class="ui fluid required dropdown" name="brand">
                                        <option value="">Select Brand</option>
                                        <option value="ALL" selected>Select All Brands</option>

                                    </select>
                                </div>
                                <div class="field">
                                    <select class="ui dropdown" name="condition">
                                        <option value=""> Select Condition</option>
                                        <option value="ALL" selected>Select All Conditions</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <select class="ui fluid required dropdown" name="series">
                                        <option value="">Select Series</option>
                                        <option value="ALL" selected>Select All Series</option>

                                    </select>
                                </div>
                                <div class="field">
                                    <select class="ui fluid required dropdown" name="issue">
                                        <option value="">Select Issue</option>
                                        <option value="ALL" selected>Select All Issues</option>
                                    </select>
                                </div>
                        </form>

                        <div class="ui containter left aligned">
                            <button class='ui left aligned button' type="button" value="Submit" id="get_button">Submit</button>
                        </div>

                    </div>
                </div>
            </div>

        </div>
        <div id="time">Last Updated: </div>
        <script>
            $('#content').css("width", window.innerWidth, "height", window.innerHeight);
            //$('#content').css("width", window.innerHeight);
            //$('.ui.container').css("margin-top", window.innerHeight/2-(600/2));
            $('#chart').attr("width", window.innerWidth*.7, "height", $('#content').height()*.6);
            $('.ui.dropdown').dropdown();
        </script>
    </body>
</html>
