function getTemperature () {

    $.get( "getTemperature.php", function( data ) {
        $("#temp1").text( data.probe1 ) ;
        $("#temp2").text( data.probe2 ) ;
        $("#temp3").text( data.probe3 ) ;
        $("#temp4").text( data.probe4 ) ;
        $("#temp5").text( data.probe5 ) ;
        $("#temp6").text( data.probeCPU ) ;

        $("#time1").text( data.time ) ;
        $("#time2").text( data.time ) ;
        $("#time3").text( data.time ) ;
        $("#time4").text( data.time ) ;
        $("#time5").text( data.time ) ;
        $("#time6").text( data.time ) ;


    }, "json" );

}

function getPowerDivs() {

    // Αλλάζει το status του συγκεκριμένου διακόπτη, μετά διαβάζει το νέο του status, το τυπώνει και αλλάζει το χρώμα του div
    for(var i=0;i<PowerDivsArray.length;i++){  
        (function(thisLooper){     // τρόπος για να παιρνάει το i μέσα στο click
            $("#"+PowerDivsArray[thisLooper]).click(function() {
                $.get( "updatePowerStatus.php?id="+PowerIDArray[thisLooper], function( data ) {
                    if(data.success=='true') {
                        $.get("getPowerStatus.php?id=" + PowerIDArray[thisLooper], function (data) {
                            $("#powerIDtext" + PowerIDArray[thisLooper]).text(data.status);
                            if(data.status=='ON')
                                $("#"+PowerDivsArray[thisLooper]).removeClass('powerOFF').addClass('powerON');
                            else $("#"+PowerDivsArray[thisLooper]).removeClass('powerON').addClass('powerOFF');
                        }, "json");
                    }
                }, "json" );
            });
        })(i);
    }

}


$(function(){

    setInterval(function(){
        getTemperature();


    }, 5000);

    getPowerDivs();
});