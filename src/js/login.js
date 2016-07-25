/**
 * Created by TheodoreBelanger on 6/8/16.
 */
$(document).ready(function(){
    $(document).on('click', "#get_button", function(){
        $("#results").remove();
        var brand = $("select[name=brand]").val();
        var condition = $("select[name=condition]").val();
        var series = $("select[name=series]").val();
        var issue = $("select[name=issue]").val();

        var args = [];




        $.ajax({
            type: "GET",
            url: "src/php/search_retrieve.php",
            data: {
                args: args
            },
            dataType: "text"
        }).done(function(response){
            console.log(response);
            $('.ui.container').append(response);
            $('.sortable.table').tablesort();
        });
    });
});
