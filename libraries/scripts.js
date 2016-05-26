var SensorKeyPressed=false;
var PowerKeyPressed=false;

function updateSensor(id) {
    room=$("#SensorID"+id).find('input[name="room"]').val();
    sensor_name=$("#SensorID"+id).find('input[name="sensor_name"]').val();
    db_field=$("#SensorID"+id).find('input[name="db_field"]').val();

    callFile="updateSensor.php?id="+id+"&room="+room+"&sensor_name="+sensor_name+"&db_field="+db_field;
    
    $.get( callFile, function( data ) {
        if(data.success=='true') {

            if (id==0) {   // αν έχει γίνει εισαγωγή νέας εγγρσφής, αλλάζει τα ονόματα των elements σχετικά
                SensorKeyPressed=false;
                LastInserted=data.lastInserted;
                $("#SensorID0").prop('id','SensorID'+LastInserted);
                $("#SensorID"+LastInserted).find('button[name="update_sensor"]').attr("onclick", "updateSensor("+LastInserted+")");
                $("#SensorID"+LastInserted).find('button[name="delete_sensor"]').attr("onclick", "deleteSensor("+LastInserted+")");
                $("#SensorID"+LastInserted).find('span').prop('id','messageID'+LastInserted);
                $("#messageID"+LastInserted).text("success");
            }
            else $("#messageID"+id).text("success");
        }
        else $("#messageID"+id).text("problem");
    }, "json" );


}

function deleteSensor(id) {
    callFile="deleteSensor.php?id="+id;

    $.get( callFile, function( data ) {
        if(data.success=='true') {

            $("#messageID"+id).text("success");
            $("#SensorID"+id).remove();
        }
        else $("#messageID"+id).text("problem");
    }, "json" );

}


function deletePower(id) {
    callFile="deletePower.php?id="+id;

    $.get( callFile, function( data ) {
        if(data.success=='true') {

            $("#messagePowerID"+id).text("success");
            $("#PowerID"+id).remove();
        }
        else $("#messagePowerID"+id).text("problem");
    }, "json" );

}

function updatePower(id) {
    room=$("#PowerID"+id).find('input[name="room"]').val();
    power_name=$("#PowerID"+id).find('input[name="power_name"]').val();
   

    callFile="updatePower.php?id="+id+"&room="+room+"&power_name="+power_name;

    $.get( callFile, function( data ) {
        if(data.success=='true') {
            if (id == 0) {   // αν έχει γίνει εισαγωγή νέας εγγρσφής, αλλάζει τα ονόματα των elements σχετικά
                PowerKeyPressed = false;
                LastInserted = data.lastInserted;
                $("#PowerID0").prop('id', 'PowerID' + LastInserted);
                $("#PowerID" + LastInserted).find('button[name="update_power"]').attr("onclick", "updatePower(" + LastInserted + ")");
                $("#PowerID" + LastInserted).find('button[name="delete_power"]').attr("onclick", "deletePower(" + LastInserted + ")");
                $("#PowerID" + LastInserted).find('span').prop('id', 'messagePowerID' + LastInserted);
                $("#messagePowerID" + LastInserted).text("success");
            }
            else $("#messagePowerID"+id).text("success");
        }
        else $("#messagePowerID"+id).text("problem");
    }, "json" );

}


function insertSensor() {
    if(!SensorKeyPressed) {
        // clone last div row
        $('div[id^="SensorID"]:last').clone().insertAfter('div[id^="SensorID"]:last').prop('id','SensorID0');
        $("#SensorID0").find('input').val('');   // clear field values
        $("#SensorID0").find('span').text('');
        // αλλάζει την function στο button
        $("#SensorID0").find('button[name="update_sensor"]').attr("onclick", "updateSensor(0)");
        $("#SensorID0").find('button[name="delete_sensor"]').attr("onclick", "deleteSensor(0)");
        $("#SensorID0").find('span').prop('id','messageID0');
        SensorKeyPressed=true;
    }
}

function insertPower() {

    if(!PowerKeyPressed) {
        // clone last div row
        $('div[id^="PowerID"]:last').clone().insertAfter('div[id^="PowerID"]:last').prop('id','PowerID0');
        $("#PowerID0").find('input').val('');   // clear field values
        $("#PowerID0").find('span').text('');
        // αλλάζει την function στο button
        $("#PowerID0").find('button[name="update_power"]').attr("onclick", "updatePower(0)");
        $("#PowerID0").find('button[name="delete_power"]').attr("onclick", "deletePower(0)");
        $("#PowerID0").find('span').prop('id','messagePowerID0');
        PowerKeyPressed=true;
    }
}


// Έλεγχος αν συνεχίζουν να γίνονται inserts στην βάση
function checkIfMysqlIsAlive() {
    $.get( "checkIfMysqlIsAlive.php", function( data ) {   // Αλλαγή του status
        if (data.DBStatus==true)
            $("#MysqlStatusText").text("ON").removeClass('SensorsOFF').addClass('SensorsON');
        else $("#MysqlStatusText").text("OFF").removeClass('SensorsON').addClass('SensorsOFF');


    }, "json" );
    
}



// Τραβάει τις θερμοκρασίες και τις τυπώνει στο κατάλληλο σημείο
function getTemperature () {


        $.get( "getTemperature.php", function( data ) {   // Αλλαγή του status
            for(var i=0;i<SensorsIDArray.length;i++) {
                
                probeText=eval("data.probe"+SensorsIDArray[i]);  // μετατροπή του sting σε όνομα μεταβλητής

                $("#temp"+SensorsIDArray[i]).text( probeText ) ;
                $("#time"+SensorsIDArray[i]).text( data.time) ;

            }

        }, "json" );


}


// Ελέγχει τις τιμές των διακοπτών, τις τυπώνει, τις αλλάζει αναλόγως όταν πατηθούν
function getPowerDivs() {

    // Αλλάζει το status του συγκεκριμένου διακόπτη, διαβάζει το νέο του status, το τυπώνει και αλλάζει το χρώμα του div
    for(var i=0;i<PowerDivsArray.length;i++){  
        (function(thisLooper){     // τρόπος για να παιρνάει το i μέσα στο click
            $("#"+PowerDivsArray[thisLooper]).click(function() {
                $.get( "updatePowerStatus.php?id="+PowerIDArray[thisLooper], function( data ) {   // Αλλαγή του status
                    if(data.success=='true') {

                        $("#powerIDtext" + PowerIDArray[thisLooper]).text(data.status);   // εμφάνιση του νέου status
                        if(data.status=='ON')      // αλλαγή της κλάσης για να αλλάξει το χρώμα
                            $("#"+PowerDivsArray[thisLooper]).removeClass('powerOFF').addClass('powerON');
                        else $("#"+PowerDivsArray[thisLooper]).removeClass('powerON').addClass('powerOFF');

                    }
                }, "json" );
            });
        })(i);
    }

}


$(function(){

    setInterval(function(){
        getTemperature();
        checkIfMysqlIsAlive();

    }, 5000);

    getPowerDivs();

});

