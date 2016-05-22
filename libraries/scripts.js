function getTemperature (DivNumber,FieldName) {

    $.get( "getTemperature.php?field="+FieldName, function( data ) {
        $("#temp"+DivNumber).text( data.fieldname ) ;
        $("#time"+DivNumber).text( data.time ) ;

    }, "json" );

}


$(function(){

    setInterval(function(){
        getTemperature("1","probe1");
        getTemperature("2","probe2");
        getTemperature("3","probe3");
        getTemperature("4","probe4");
        getTemperature("5","probe5");
        getTemperature("6","probeCPU");

    }, 3000);


});