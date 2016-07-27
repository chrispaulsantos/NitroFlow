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
            // console.log(csvData);
            createUID(csvData);
        }
    });
}

function createUID(csvData){
    l = csvData.length;

    for(var i = 0; i < l; i++){

        var zip = csvData[i]["Zip"].toString();
        if(zip.length == 4){
            zip = "0" + zip;
        }

        var vendor = csvData[i]["Vendor"].toString();
        for(var j = 0; vendor.length < 4; j++){
            vendor = "0" + vendor;
        }

        var num = csvData[i]["Number"].toString();
        for(var j = 0; num.length < 4; j++){
            num = "0" + num;
        }

        var UID = "$UID" + zip + vendor + num;
        console.log(UID);
    }
}