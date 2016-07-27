/**
 * Created by chris on 7/27/16.
 */
$(document).ready(function(){
    $("#file").change(handleFileSelect);
})

function handleFileSelect(evt) {
    $('#load .dimmer').addClass("active");
    var file = evt.target.files[0];
    Papa.parse(file, {
        header: true,
        dynamicTyping: true,
        complete: function(results) {
            csvData = results["data"];
            console.log(csvData);
            createUID(csvData);
        }
    });
}

function createUID(csvData){
    csvData.forEach(function(loc){
        console.log(loc.address);
    });
}