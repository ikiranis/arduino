//
// File: scripts.js
// Created by rocean
// Date: 20/05/16
// Time: 19:44
// Javascript controls και functions
//

var UserKeyPressed=false;
var AlertKeyPressed=false;
var SensorKeyPressed=false;
var PowerKeyPressed=false;  // Αν δεν έχει πατηθεί η εισαγωγή νέας γραμμής είναι false, αλλιώς true για να μην μπορεί να ξαναπατηθεί

var getTemps;
var db_field;


// Ενημερώνει την υπάρχουσα εγγραφή στην βάση στο table alerts, ή εισάγει νέα εγγραφή
function updateUser(id) {
    username=$("#UserID"+id).find('input[name="username"]').val();
    email=$("#UserID"+id).find('input[name="email"]').val();
    password=$("#UserID"+id).find('input[name="password"]').val();
    repeat_password=$("#UserID"+id).find('input[name="repeat_password"]').val();
    usergroup=$("#UserID"+id).find('select[name="usergroup"]').val();
    fname=$("#UserID"+id).find('input[name="fname"]').val();
    lname=$("#UserID"+id).find('input[name="lname"]').val();

    if (password=='') changepass=false;
    else changepass=true;

    console.log(id+' '+username+' '+email+' '+password+' '+repeat_password+' '+usergroup+' '+fname+' '+lname+ ' '+changepass);

    if(changepass)
        callFile="updateUser.php?id="+id+"&username="+username+"&email="+email+"&password="+password+
        "&usergroup="+usergroup+"&fname="+fname+"&lname="+lname;
    else callFile="updateUser.php?id="+id+"&username="+username+"&email="+email+
        "&usergroup="+usergroup+"&fname="+fname+"&lname="+lname;

    $.get( callFile, function( data ) {
        console.log(data.success);

        if(data.success=='true') {

            if (id==0) {   // αν έχει γίνει εισαγωγή νέας εγγρσφής, αλλάζει τα ονόματα των elements σχετικά
                UserKeyPressed=false;
                LastInserted=data.lastInserted;
                $("#UserID0").prop('id','UserID'+LastInserted);
                $("#UserID"+LastInserted).find('button[name="update_user"]')
                    .attr("onclick", "updateUser("+LastInserted+")");
                $("#UserID"+LastInserted).find('button[name="delete_user"]')
                    .attr("onclick", "deleteUser("+LastInserted+")");
                $("#UserID"+LastInserted).find('span[id^="messageUserID"]').prop('id','messageUserID'+LastInserted);
                $("#messageUserID"+LastInserted).text("success");
            }
            else $("#messageUserID"+id).text("success");
        }
        else $("#messageUserID"+id).text("problem");
    }, "json" );


}



// Ενημερώνει την υπάρχουσα εγγραφή στην βάση στο table alerts, ή εισάγει νέα εγγραφή
function updateAlert(id) {
    email=$("#AlertID"+id).find('input[name="email"]').val();
    time_limit=$("#AlertID"+id).find('input[name="time_limit"]').val();
    temp_limit=$("#AlertID"+id).find('input[name="temp_limit"]').val();
    sensors_id=$("#AlertID"+id).find('select[name="sensors_list"]').val();
    user_id=$("#AlertID"+id).find('input[name="user_id"]').val();

    // console.log(email+' '+time_limit+' '+temp_limit+' '+sensors_id+' '+user_id);
    

    callFile="updateAlert.php?id="+id+"&email="+email+"&time_limit="+time_limit+
        "&temp_limit="+temp_limit+"&sensors_id="+sensors_id+"&user_id="+user_id;

    $.get( callFile, function( data ) {
        if(data.success=='true') {

            if (id==0) {   // αν έχει γίνει εισαγωγή νέας εγγρσφής, αλλάζει τα ονόματα των elements σχετικά
                AlertKeyPressed=false;
                LastInserted=data.lastInserted;
                $("#AlertID0").prop('id','AlertID'+LastInserted);
                $("#AlertID"+LastInserted).find('button[name="update_alert"]')
                    .attr("onclick", "updateAlert("+LastInserted+")");
                $("#AlertID"+LastInserted).find('button[name="delete_alert"]')
                    .attr("onclick", "deleteAlert("+LastInserted+")");
                $("#AlertID"+LastInserted).find('span[id^="messageAlertID"]').prop('id','messageAlertID'+LastInserted);
                $("#messageAlertID"+LastInserted).text("success");
            }
            else $("#messageAlertID"+id).text("success");
        }
        else $("#messageAlertID"+id).text("problem");
    }, "json" );


}


// Ενημερώνει την υπάρχουσα εγγραφή στην βάση στο table sensors, ή εισάγει νέα εγγραφή
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
                $("#SensorID"+LastInserted).find('span[id^="messageID"]').prop('id','messageID'+LastInserted);
                $("#messageID"+LastInserted).text("success");
            }
            else $("#messageID"+id).text("success");
        }
        else $("#messageID"+id).text("problem");
    }, "json" );


}


// Ενημερώνει την υπάρχουσα εγγραφή στην βάση στο table power, ή εισάγει νέα εγγραφή
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
                $("#PowerID" + LastInserted).find('span[id^="messagePowerID"]').prop('id', 'messagePowerID' + LastInserted);
                $("#messagePowerID" + LastInserted).text("success");
            }
            else $("#messagePowerID"+id).text("success");
        }
        else $("#messagePowerID"+id).text("problem");
    }, "json" );

}

// Σβήνει την εγγραφή στο sensors
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

// Σβήνει την εγγραφή στο power
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

// Σβήνει την εγγραφή στο alerts
function deleteAlert(id) {
    callFile="deleteAlert.php?id="+id;

    $.get( callFile, function( data ) {
        if(data.success=='true') {

            $("#messageAlertID"+id).text("success");
            $("#AlertID"+id).remove();
        }
        else $("#messageAlertID"+id).text("problem");
    }, "json" );

}

// Σβήνει την εγγραφή στο user, user_details, salts
function deleteUser(id) {
    callFile="deleteUser.php?id="+id;

    $.get( callFile, function( data ) {
        console.log(data.success);
        if(data.success=='true') {

            $("#messageUserID"+id).text("success");
            $("#UserID"+id).remove();
        }
        else $("#messageUserID"+id).text("problem");
    }, "json" );

}


// Εισάγει νέα div γραμμή αντιγράφοντας την τελευταία και μηδενίζοντας τις τιμές που είχε η τελευταία
function insertUser() {
    if(!UserKeyPressed) {

        // clone last div row
        $('div[id^="UserID"]:last').clone().insertAfter('div[id^="UserID"]:last').prop('id','UserID0');
        $("#UserID0").find('input[name="username"]').val(''); // clear field values
        $("#UserID0").find('input[name="email"]').val('');
        $("#UserID0").find('input[name="fname"]').val('');
        $("#UserID0").find('input[name="lname"]').val('');
        $("#UserID0").find('span[id^="messageUserID"]').text('').prop('id','messageUserID0');
        // αλλάζει την function στο button
        $("#UserID0").find('button[name="update_user"]').attr("onclick", "updateUser(0)");
        $("#UserID0").find('button[name="delete_user"]').attr("onclick", "deleteUser(0)");
        UserKeyPressed=true;
    }
}

// Εισάγει νέα div γραμμή αντιγράφοντας την τελευταία και μηδενίζοντας τις τιμές που είχε η τελευταία
function insertAlert() {
    if(!AlertKeyPressed) {

        // clone last div row
        $('div[id^="AlertID"]:last').clone().insertAfter('div[id^="AlertID"]:last').prop('id','AlertID0');
        $("#AlertID0").find('input[name="email"]').val('');   // clear field values
        $("#AlertID0").find('input[name="time_limit"]').val('');
        $("#AlertID0").find('input[name="temp_limit"]').val('');
        $("#AlertID0").find('input[name="sensors_id"]').val('');
        $("#AlertID0").find('span[id^="messageAlertID"]').text('').prop('id','messageAlertID0');
        // αλλάζει την function στο button
        $("#AlertID0").find('button[name="update_alert"]').attr("onclick", "updateAlert(0)");
        $("#AlertID0").find('button[name="delete_alert"]').attr("onclick", "deleteAlert(0)");
        AlertKeyPressed=true;
    }
}

// Εισάγει νέα div γραμμή αντιγράφοντας την τελευταία και μηδενίζοντας τις τιμές που είχε η τελευταία
function insertSensor() {
    if(!SensorKeyPressed) {
        // clone last div row
        $('div[id^="SensorID"]:last').clone().insertAfter('div[id^="SensorID"]:last').prop('id','SensorID0');
        $("#SensorID0").find('input').val('');   // clear field values
        $("#SensorID0").find('span[id^="messageID"]').text('').prop('id','messageID0');
        // αλλάζει την function στο button
        $("#SensorID0").find('button[name="update_sensor"]').attr("onclick", "updateSensor(0)");
        $("#SensorID0").find('button[name="delete_sensor"]').attr("onclick", "deleteSensor(0)");
        SensorKeyPressed=true;
    }
}

// Εισάγει νέα div γραμμή αντιγράφοντας την τελευταία και μηδενίζοντας τις τιμές που είχε η τελευταία
function insertPower() {

    if(!PowerKeyPressed) {
        // clone last div row
        $('div[id^="PowerID"]:last').clone().insertAfter('div[id^="PowerID"]:last').prop('id','PowerID0');
        $("#PowerID0").find('input').val('');   // clear field values
        $("#PowerID0").find('span[id^="messagePowerID"]').text('').prop('id','messagePowerID0');
        // αλλάζει την function στο button
        $("#PowerID0").find('button[name="update_power"]').attr("onclick", "updatePower(0)");
        $("#PowerID0").find('button[name="delete_power"]').attr("onclick", "deletePower(0)");
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
                
                probeText=eval("data.LastTemps.probe"+SensorsIDArray[i]);  // μετατροπή του sting σε όνομα μεταβλητής
                AvgTempText=eval("data.AvgTemps.temp"+SensorsIDArray[i]);

                NewTemp=parseInt(probeText);
                AvgTemp=parseInt(AvgTempText);


                if(AvgTemp>NewTemp) {
                    $("#TempBlock"+SensorsIDArray[i]).removeClass('warm').removeClass('equal').addClass('cold');
                    $("#diff"+SensorsIDArray[i]).html('&#x21E9');
                }
                if(AvgTemp<NewTemp) {
                    $("#TempBlock"+SensorsIDArray[i]).removeClass('cold').removeClass('equal').addClass('warm');
                    $("#diff"+SensorsIDArray[i]).html('&#x21E7');
                }
                if(AvgTemp==NewTemp) {
                    $("#TempBlock"+SensorsIDArray[i]).removeClass('warm').removeClass('cold').addClass('equal');
                    $("#diff"+SensorsIDArray[i]).html('-');
                }

                $("#temp"+SensorsIDArray[i]).text( probeText ) ;
                $("#time"+SensorsIDArray[i]).text( data.LastTemps.time) ;

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


// Ελέγχει συνεχώς τις θερμοκρασίες
function CheckTemperatures() {

    setInterval(function(){
        getTemperature();

    }, IntervalValue*1000);
}


// μετράει τα πεδία ενός json object
function countjson(obj) {
    var count=0;
    for(var prop in obj) {
        if (obj.hasOwnProperty(prop)) {
            ++count;
        }
    }
    return count;
}



// Callback that creates and populates a data table,
    // instantiates the pie chart, passes in the data and
    // draws it.
function drawChart() {
    // Create the data table.
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Time');
    data.addColumn('number', 'Temperature');
    data.addRows(countjson(getTemps.times));

    for(var i=0;i<countjson(getTemps.times);i++) {  // γέμισμα του chart με τις τιμές από την getTemps

         time=eval("getTemps.times.time"+i);
         temp=parseInt(eval("getTemps.temps.temp"+i));

        data.setCell(i, 0, time);
        data.setCell(i, 1, temp);
    }

    // Set chart options
        var options = {

            legend: {position: 'top'},
            hAxis: {
              
                direction:-1



            },
            vAxis: {
                title: 'Temperatures'

            }

        };

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
    chart.draw(data, options);
}


function RunStatistics() {

    db_field=$("#SelectGraph").find('select[name="sensors_list"]').val();
    date_limit=$("#SelectGraph").find('select[name="date_list"]').val();

    callFile="getStatistics.php?db_field="+db_field+"&date_limit="+date_limit;

        $.get( callFile, function( data ) {

            getTemps=JSON.parse(data);   // παίρνει τα json data σαν array

                // Set a callback to run when the Google Visualization API is loaded.
                google.charts.setOnLoadCallback(drawChart);

        });

}



$(function(){
    // Load the Visualization API and the corechart package.
    google.charts.load('current', {'packages':['corechart']});

    setInterval(function(){
        checkIfMysqlIsAlive();

    }, IntervalValue*1000);

    



});



