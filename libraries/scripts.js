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


// extension στην jquery. Προσθέτει την addClassDelay. π.χ. $('div').addClassDelay('somedivclass',3000)
// Προσθέτει μια class και την αφερεί μετά από λίγο
$.fn.addClassDelay = function(className,delay) {
    var $addClassDelayElement = $(this), $addClassName = className;
    $addClassDelayElement.addClass($addClassName);
    setTimeout(function(){
        $addClassDelayElement.removeClass($addClassName);
    },delay);
};


// extension του jquery που επιστρέφει την λίστα των κλάσεων ενός element, σε array
// π.χ myClasses= $("#AlertID"+id).find('input[name=delete_alert]').classes();
!(function ($) {
    $.fn.classes = function (callback) {
        var classes = [];
        $.each(this, function (i, v) {
            var splitClassName = v.className.split(/\s+/);
            for (var j in splitClassName) {
                var className = splitClassName[j];
                if (-1 === classes.indexOf(className)) {
                    classes.push(className);
                }
            }
        });
        if ('function' === typeof callback) {
            for (var i in classes) {
                callback(classes[i]);
            }
        }
        return classes;
    };
})(jQuery);


function DisplayMessage (element, error) {
    $(element).text(error);
    $(element).show('slow').delay(5000).hide('fast');
}


// Παίρνει την τελευταία θερμοκρασία της CPU και το τυπώνει στο element
function checkCPUtemp(element) {
    $.get( "checkCPUtemp.php", function( data ) {   // Αλλαγή του status
        if (data.lastCPUtemp)
            $(element).text(data.lastCPUtemp);
        else $(element).text("0");


    }, "json" );
}


// Εισαγωγή αρχικού χρήστη admin
function registerUser() {
    username = $("#RegisterUserWindow").find('input[name="username"]').val();
    email = $("#RegisterUserWindow").find('input[name="email"]').val();
    password = $("#RegisterUserWindow").find('input[name="password"]').val();
    repeat_password = $("#RegisterUserWindow").find('input[name="repeat_password"]').val();

    if ($('#RegisterForm').valid()) {


        callFile = "registerUser.php?username=" + username + "&password=" + password + "&email=" + email;

        $.get(callFile, function (data) {

            result = JSON.parse(data);
            console.log(result['success']);
            if (result['success'] == true) {

                window.location.href = "index.php";
            }
            else  DisplayMessage('#alert_error',result['message']);

        });


    }

}


// Έλεγχος του login
function login() {
        username = $("#LoginWindow").find('input[name="username"]').val();
        password = $("#LoginWindow").find('input[name="password"]').val();
        if ($("#LoginWindow").find('input[name="SavePassword"]').is(":checked"))
            SavePassword = true;
        else SavePassword = false;

        if ($('#LoginForm').valid()) {


            callFile = "checkLogin.php?username=" + username + "&password=" + password + "&SavePassword=" + SavePassword;

            $.get(callFile, function (data) {

                result = JSON.parse(data);
                console.log(result['success']);
                if (result['success'] == true) {

                    window.location.href = "index.php";
                }
                else  DisplayMessage('#alert_error',result['message']);

            });

        }

}




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

    // console.log(id+' '+username+' '+email+' '+password+' '+repeat_password+' '+usergroup+' '+fname+' '+lname+ ' '+changepass);

    if(changepass)
        callFile="updateUser.php?id="+id+"&username="+username+"&email="+email+"&password="+password+
        "&usergroup="+usergroup+"&fname="+fname+"&lname="+lname;
    else callFile="updateUser.php?id="+id+"&username="+username+"&email="+email+
        "&usergroup="+usergroup+"&fname="+fname+"&lname="+lname;



    if ( $('#users_formID'+id).valid() && password==repeat_password ) {

        $.get(callFile, function (data) {

            if (data.success == true) {
                // console.log(data.success);

                if (id == 0) {   // αν έχει γίνει εισαγωγή νέας εγγρσφής, αλλάζει τα ονόματα των elements σχετικά
                    UserKeyPressed = false;
                    LastInserted = data.lastInserted;
                    $("#UserID0").prop('id', 'UserID' + LastInserted);
                    $("#UserID" + LastInserted).find('form').prop('id','users_formID'+ LastInserted);
                    $("#UserID" + LastInserted).find('input[name="update_user"]')
                        .attr("onclick", "updateUser(" + LastInserted + ")");
                    $("#UserID" + LastInserted).find('input[name="delete_user"]')
                        .attr("onclick", "deleteUser(" + LastInserted + ")");
                    $("#UserID" + LastInserted).find('input[id^="messageUserID"]').prop('id', 'messageUserID' + LastInserted);
                    $("#messageUserID" + LastInserted).addClassDelay("success", 3000);
                }
                else $("#messageUserID" + id).addClassDelay("success", 3000);
            }
            else if(data.UserExists) {
                    $("#messageUserID" + id).addClassDelay("failure", 3000);

                    DisplayMessage('#alert_error', error1+' '+username+' '+error2);
                } else $("#messageUserID" + id).addClassDelay("failure", 3000);

        }, "json");
    }

}



// Ενημερώνει την υπάρχουσα εγγραφή στην βάση στο table alerts, ή εισάγει νέα εγγραφή
function updateAlert(id) {
    email=$("#AlertID"+id).find('input[name="email"]').val();
    time_limit=$("#AlertID"+id).find('input[name="time_limit"]').val();
    temp_limit=$("#AlertID"+id).find('input[name="temp_limit"]').val();
    sensors_id=$("#AlertID"+id).find('select[name="sensors_list"]').val();
    user_id=$("#AlertID"+id).find('input[name="user_id"]').val();



    callFile="updateAlert.php?id="+id+"&email="+email+"&time_limit="+time_limit+
        "&temp_limit="+temp_limit+"&sensors_id="+sensors_id+"&user_id="+user_id;

    if ($('#alerts_formID'+id).valid()) {
        $.get(callFile, function (data) {
            if (data.success == 'true') {

                if (id == 0) {   // αν έχει γίνει εισαγωγή νέας εγγρσφής, αλλάζει τα ονόματα των elements σχετικά
                    AlertKeyPressed = false;
                    LastInserted = data.lastInserted;
                    $("#AlertID0").prop('id', 'AlertID' + LastInserted);
                    $("#AlertID" + LastInserted).find('form').prop('id','alerts_formID'+ LastInserted);
                    $("#AlertID" + LastInserted).find('input[name="update_alert"]')
                        .attr("onclick", "updateAlert(" + LastInserted + ")");
                    $("#AlertID" + LastInserted).find('input[name="delete_alert"]')
                        .attr("onclick", "deleteAlert(" + LastInserted + ")");
                    $("#AlertID" + LastInserted).find('input[id^="messageAlertID"]').prop('id', 'messageAlertID' + LastInserted);
                    $("#messageAlertID" + LastInserted).addClassDelay("success", 3000);
                }
                else $("#messageAlertID" + id).addClassDelay("success", 3000);
            }
            else $("#messageAlertID" + id).addClassDelay("failure", 3000);
        }, "json");
    }

}


// Ενημερώνει την υπάρχουσα εγγραφή στην βάση στο table sensors, ή εισάγει νέα εγγραφή
function updateSensor(id) {
    room=$("#SensorID"+id).find('input[name="room"]').val();
    sensor_name=$("#SensorID"+id).find('input[name="sensor_name"]').val();
    db_field=$("#SensorID"+id).find('input[name="db_field"]').val();

    callFile="updateSensor.php?id="+id+"&room="+room+"&sensor_name="+sensor_name+"&db_field="+db_field;



    if ($('#sensors_formID'+id).valid()) {
        $.get(callFile, function (data) {
            if (data.success == 'true') {

                if (id == 0) {   // αν έχει γίνει εισαγωγή νέας εγγρσφής, αλλάζει τα ονόματα των elements σχετικά
                    SensorKeyPressed = false;
                    LastInserted = data.lastInserted;
                    $("#SensorID0").prop('id', 'SensorID' + LastInserted);
                    $("#SensorID" + LastInserted).find('form').prop('id','sensors_formID'+ LastInserted);
                    $("#SensorID" + LastInserted).find('input[name="update_sensor"]').attr("onclick", "updateSensor(" + LastInserted + ")");
                    $("#SensorID" + LastInserted).find('input[name="delete_sensor"]').attr("onclick", "deleteSensor(" + LastInserted + ")");
                    $("#SensorID" + LastInserted).find('input[id^="messageID"]').prop('id', 'messageID' + LastInserted);
                    $("#messageID" + LastInserted).addClassDelay("success", 3000);
                }
                else $("#messageID" + id).addClassDelay("success", 3000);
            }
            else $("#messageID" + id).addClassDelay("failure", 3000);
        }, "json");
    }

}

// Ενημερώνει την υπάρχουσα εγγραφή στην βάση στο table options, ή εισάγει νέα εγγραφή
function updateOption(id) {
    option_name=$("#OptionID"+id).find('input[name="option_name"]').val();
    option_value=$("#OptionID"+id).find('input[name="option_value"]').val();


    callFile="updateOption.php?id="+id+"&option_name="+option_name+"&option_value="+encodeURIComponent(option_value);


    // console.log(callFile);

    if ($('#options_formID'+id).valid()) {
        $.get(callFile, function (data) {
            if (data.success == 'true') {

               $("#messageOptionID" + id).addClassDelay("success", 3000);
            }
            else $("#messageOptionID" + id).addClassDelay("failure", 3000);
        }, "json");
    }

}


// Ενημερώνει την υπάρχουσα εγγραφή στην βάση στο table power, ή εισάγει νέα εγγραφή
function updatePower(id) {
    room=$("#PowerID"+id).find('input[name="room"]').val();
    power_name=$("#PowerID"+id).find('input[name="power_name"]').val();
    power_mac_address=$("#PowerID"+id).find('input[name="power_mac_address"]').val();




    callFile="updatePower.php?id="+id+"&room="+room+"&power_name="+power_name+"&power_mac_address="+power_mac_address;

    if ($('#powers_formID'+id).valid()) {
        $.get(callFile, function (data) {
            if (data.success == 'true') {
                if (id == 0) {   // αν έχει γίνει εισαγωγή νέας εγγρσφής, αλλάζει τα ονόματα των elements σχετικά
                    PowerKeyPressed = false;
                    LastInserted = data.lastInserted;
                    $("#PowerID0").prop('id', 'PowerID' + LastInserted);
                    $("#PowerID" + LastInserted).find('form').prop('id','powers_formID'+ LastInserted);
                    $("#PowerID" + LastInserted).find('input[name="update_power"]').attr("onclick", "updatePower(" + LastInserted + ")");
                    $("#PowerID" + LastInserted).find('input[name="delete_power"]').attr("onclick", "deletePower(" + LastInserted + ")");
                    $("#PowerID" + LastInserted).find('input[id^="messagePowerID"]').prop('id', 'messagePowerID' + LastInserted);
                    $("#messagePowerID" + LastInserted).addClassDelay("success", 3000);
                }
                else $("#messagePowerID" + id).addClassDelay("success", 3000);
            }
            else $("#messagePowerID" + id).addClassDelay("failure", 3000);
        }, "json");
    }
}

// Σβήνει την εγγραφή στο sensors
function deleteSensor(id) {
    callFile="deleteSensor.php?id="+id;

    $.get( callFile, function( data ) {
        if(data.success=='true') {

            $("#messageID"+id).addClassDelay("success",3000);


            myClasses= $("#SensorID"+id).find('input[name=delete_sensor]').classes();   // Παίρνει τις κλάσεις του delete_alert

            if(!myClasses[2])   // Αν δεν έχει κλάση dontdelete σβήνει το div
                $("#SensorID"+id).remove();
            else {   // αλλιώς καθαρίζει μόνο τα πεδία
                $("#SensorID"+id).find('input').val('');   // clear field values
                $("#SensorID"+id).prop('id','SensorID0');
                $("#SensorID0").find('form').prop('id','sensors_formID0');
                $("#SensorID0").find('input[id^="messageID"]').text('').prop('id','messageID0');
                // αλλάζει την function στο button
                $("#SensorID0").find('input[name="update_sensor"]').attr("onclick", "updateSensor(0)");
                $("#SensorID0").find('input[name="delete_sensor"]').attr("onclick", "deleteSensor(0)");

                $('#sensors_formID0').validate({ // initialize the plugin
                    errorElement: 'div'
                });

            }
        }
        else $("#messageID"+id).addClassDelay("failure",3000);
    }, "json" );

}

// Σβήνει την εγγραφή στο power
function deletePower(id) {
    callFile="deletePower.php?id="+id;

    $.get( callFile, function( data ) {
        if(data.success=='true') {

            $("#messagePowerID"+id).addClassDelay("success",3000);


            myClasses= $("#PowerID"+id).find('input[name=delete_power]').classes();   // Παίρνει τις κλάσεις του delete_alert

            if(!myClasses[2])   // Αν δεν έχει κλάση dontdelete σβήνει το div
                $("#PowerID"+id).remove();
            else {   // αλλιώς καθαρίζει μόνο τα πεδία
                $("#PowerID"+id).find('input').val('');   // clear field values
                $("#PowerID"+id).prop('id','PowerID0');
                $("#PowerID0").find('form').prop('id','powers_formID0');
                $("#PowerID0").find('input[id^="messagePowerID"]').text('').prop('id','messagePowerID0');
                // αλλάζει την function στο button
                $("#PowerID0").find('input[name="update_power"]').attr("onclick", "updatePower(0)");
                $("#PowerID0").find('input[name="delete_power"]').attr("onclick", "deletePower(0)");

                $('#powers_formID0').validate({ // initialize the plugin
                    errorElement: 'div'
                });

            }
        }
        else $("#messagePowerID"+id).addClassDelay("failure",3000);
    }, "json" );

}

// Σβήνει την εγγραφή στο alerts
function deleteAlert(id) {
    callFile="deleteAlert.php?id="+id;

    $.get( callFile, function( data ) {
        if(data.success=='true') {

            $("#messageAlertID"+id).addClassDelay("success",3000);

            myClasses= $("#AlertID"+id).find('input[name=delete_alert]').classes();   // Παίρνει τις κλάσεις του delete_alert

            if(!myClasses[2])   // Αν δεν έχει κλάση dontdelete σβήνει το div
                $("#AlertID"+id).remove();
            else {   // αλλιώς καθαρίζει μόνο τα πεδία
                $("#AlertID"+id).prop('id','AlertID0');
                $("#AlertID0").find('form').prop('id','alerts_formID0');
                $("#AlertID0").find('input[name="email"]').val('');
                $("#AlertID0").find('input[name="time_limit"]').val('');
                $("#AlertID0").find('input[name="temp_limit"]').val('');
                $("#AlertID0").find('input[name="sensors_id"]').val('');
                $("#AlertID0").find('input[id^="messageAlertID"]').text('').prop('id','messageAlertID0');
                // αλλάζει την function στο button
                $("#AlertID0").find('input[name="update_alert"]').attr("onclick", "updateAlert(0)");
                $("#AlertID0").find('input[name="delete_alert"]').attr("onclick", "deleteAlert(0)");

                $('#alerts_formID0').validate({ // initialize the plugin
                    errorElement: 'div'
                });
            }
        }
        else $("#messageAlertID"+id).addClassDelay("failure",3000);
    }, "json" );

}

// Σβήνει την εγγραφή στο user, user_details, salts
function deleteUser(id) {
    callFile="deleteUser.php?id="+id;

    $.get( callFile, function( data ) {
        console.log(data.success);
        if(data.success=='true') {

            $("#messageUserID"+id).addClassDelay("success",3000);

            myClasses= $("#UserID"+id).find('input[name=delete_user]').classes();   // Παίρνει τις κλάσεις του delete_alert

            if(!myClasses[2])   // Αν δεν έχει κλάση dontdelete σβήνει το div
                $("#UserID"+id).remove();
            else {   // αλλιώς καθαρίζει μόνο τα πεδία
                $("#UserID"+id).find('input').val('');   // clear field values
                $("#UserID"+id).prop('id','UserID0');
                $("#UserID0").find('form').prop('id','users_formID0');
                $("#UserID0").find('input[name="email"]').val('');
                $("#UserID0").find('input[name="fname"]').val('');
                $("#UserID0").find('input[name="lname"]').val('');
                $("#UserID0").find('input[name="password"]').prop('required',true).prop('id','password0');
                $("#UserID0").find('input[name="repeat_password"]').prop('required',true).prop('id','0');
                $("#UserID0").find('input[id^="messageUserID"]').text('').prop('id','messageUserID0');
                // αλλάζει την function στο button
                $("#UserID0").find('input[name="update_user"]').attr("onclick", "updateUser(0)");
                $("#UserID0").find('input[name="delete_user"]').attr("onclick", "deleteUser(0)");


                $('#users_formID0').validate({ // initialize the plugin
                    errorElement: 'div'
                });

            }


        }
        else $("#messageUserID"+id).addClassDelay("failure",3000);
    }, "json" );

}


// Εισάγει νέα div γραμμή αντιγράφοντας την τελευταία και μηδενίζοντας τις τιμές που είχε η τελευταία
function insertUser() {
    if(!UserKeyPressed) {

        // clone last div row
        $('div[id^="UserID"]:last').clone().insertAfter('div[id^="UserID"]:last').prop('id','UserID0');
        $("#UserID0").find('input[name="username"]').val(''); // clear field values
        $("#UserID0").find('form').prop('id','users_formID0');
        $("#UserID0").find('input[name="email"]').val('');
        $("#UserID0").find('input[name="fname"]').val('');
        $("#UserID0").find('input[name="lname"]').val('');
        $("#UserID0").find('input[name="password"]').prop('required',true).prop('id','password0');
        $("#UserID0").find('input[name="repeat_password"]').prop('required',true).prop('id','0');
        $("#UserID0").find('input[id^="messageUserID"]').text('').removeClass('success').prop('id','messageUserID0');
        // αλλάζει την function στο button
        $("#UserID0").find('input[name="update_user"]').attr("onclick", "updateUser(0)");
        $("#UserID0").find('input[name="delete_user"]').attr("onclick", "deleteUser(0)");
        UserKeyPressed=true;




        $('#users_formID0').validate({ // initialize the plugin
            errorElement: 'div'
            // rules : {
            //     repeat_password: {
            //         equalTo : '[name="password"]'
            //     }
            // }
        });



    }
}

// Εισάγει νέα div γραμμή αντιγράφοντας την τελευταία και μηδενίζοντας τις τιμές που είχε η τελευταία
function insertAlert() {
    if(!AlertKeyPressed) {

        // clone last div row
        $('div[id^="AlertID"]:last').clone().insertAfter('div[id^="AlertID"]:last').prop('id','AlertID0');
        $("#AlertID0").find('input[name="email"]').val('');   // clear field values
        $("#AlertID0").find('form').prop('id','alerts_formID0');
        $("#AlertID0").find('input[name="time_limit"]').val('');
        $("#AlertID0").find('input[name="temp_limit"]').val('');
        $("#AlertID0").find('input[name="sensors_id"]').val('');
        $("#AlertID0").find('input[id^="messageAlertID"]').text('').removeClass('success').prop('id','messageAlertID0');
        // αλλάζει την function στο button
        $("#AlertID0").find('input[name="update_alert"]').attr("onclick", "updateAlert(0)");
        $("#AlertID0").find('input[name="delete_alert"]').attr("onclick", "deleteAlert(0)");
        AlertKeyPressed=true;

        $('#alerts_formID0').validate({ // initialize the plugin
            errorElement: 'div'
        });
    }
}

// Εισάγει νέα div γραμμή αντιγράφοντας την τελευταία και μηδενίζοντας τις τιμές που είχε η τελευταία
function insertSensor() {
    if(!SensorKeyPressed) {
        
        // clone last div row
        $('div[id^="SensorID"]:last').clone().insertAfter('div[id^="SensorID"]:last').prop('id','SensorID0');
        $("#SensorID0").find('input').val('');   // clear field values
        $("#SensorID0").find('form').prop('id','sensors_formID0');
        $("#SensorID0").find('input[id^="messageID"]').text('').removeClass('success').prop('id','messageID0');
        // αλλάζει την function στο button
        $("#SensorID0").find('input[name="update_sensor"]').attr("onclick", "updateSensor(0)");
        $("#SensorID0").find('input[name="delete_sensor"]').attr("onclick", "deleteSensor(0)");
        SensorKeyPressed=true;

        $('#sensors_formID0').validate({ // initialize the plugin
            errorElement: 'div'
        });


    }
    // $("#sensors_form").initValidation();

}

// Εισάγει νέα div γραμμή αντιγράφοντας την τελευταία και μηδενίζοντας τις τιμές που είχε η τελευταία
function insertPower() {

    if(!PowerKeyPressed) {
        // clone last div row
        $('div[id^="PowerID"]:last').clone().insertAfter('div[id^="PowerID"]:last').prop('id','PowerID0');
        $("#PowerID0").find('input').val('');   // clear field values
        $("#PowerID0").find('form').prop('id','powers_formID0');
        $("#PowerID0").find('input[id^="messagePowerID"]').text('').removeClass('success').prop('id','messagePowerID0');
        // αλλάζει την function στο button
        $("#PowerID0").find('input[name="update_power"]').attr("onclick", "updatePower(0)");
        $("#PowerID0").find('input[name="delete_power"]').attr("onclick", "deletePower(0)");
        PowerKeyPressed=true;

        $('#powers_formID0').validate({ // initialize the plugin
            errorElement: 'div'
        });


    }
}


// Έλεγχος αν συνεχίζουν να γίνονται inserts στην βάση
function checkIfMysqlIsAlive(element) {

    $.get( "getDBstatus.php", function( data ) {   // Αλλαγή του status
        if (data.DBStatus=="on")
            $(element).text("ON").removeClass('SensorsOFF').addClass('SensorsON');
        else $(element).text("OFF").removeClass('SensorsON').addClass('SensorsOFF');


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
                }
                if(AvgTemp<NewTemp) {
                    $("#TempBlock"+SensorsIDArray[i]).removeClass('cold').removeClass('equal').addClass('warm');
                }
                if(AvgTemp==NewTemp) {
                    $("#TempBlock"+SensorsIDArray[i]).removeClass('warm').removeClass('cold').addClass('equal');
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

                        // Αλλαγή της τιμής στην βάση
                        $("#powerIDtext" + PowerIDArray[thisLooper]).text(data.status);   // εμφάνιση του νέου status
                        if(data.status=='ON')      // αλλαγή της κλάσης για να αλλάξει το χρώμα
                            $("#"+PowerDivsArray[thisLooper]).removeClass('powerOFF').addClass('powerON');
                        else $("#"+PowerDivsArray[thisLooper]).removeClass('powerON').addClass('powerOFF');

                        // Αλλαγή του relay
                        // TODO καλύτερο έλεγχο
                        $.get('http://' + data.relayIP + '/' + data.status, function(result) {
                            if(result==0) {
                                var status = 'OFF';
                                $("#powerIDtext" + PowerIDArray[thisLooper]).text(status);   // εμφάνιση του νέου status
                                $("#"+PowerDivsArray[thisLooper]).removeClass('powerON').addClass('powerOFF');
                            }
                        });

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
    // Source https://developers.google.com/chart/
function drawChart() {
    backgroundColor=$('section').css( "background-color" );  //  To background color του parent element
    lineColor=$('section').css( "color" );

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
            colors: [lineColor],
            hAxis: {
              
                direction:-1



            },
            vAxis: {
                title: 'Temperatures'

            },

            backgroundColor: backgroundColor,
            is3D: true

        };

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
    chart.draw(data, options);
}


function RunStatistics() {

    db_field=$("#SelectGraph").find('select[name="sensors_list"]').val();
    date_limit=$("#SelectGraph").find('select[name="date_list"]').val();

    callFile="getStatistics.php?db_field="+db_field+"&date_limit="+date_limit;
    $('#progress').show();

        $.get( callFile, function( data ) {

            getTemps=JSON.parse(data);   // παίρνει τα json data σαν array

                // Set a callback to run when the Google Visualization API is loaded.
                google.charts.setOnLoadCallback(drawChart);

            $('#progress').hide();

        });

}

// Προσθέτει το 0 μπροστά από τον αριθμό όταν είναι κάτω από το 10
function addZero(i) {
    if (i < 10) {
        i = "0" + i;
    }
    return i;
}

// Επιστρέφει την τρέχουσα ώρα σε string και το εμφανίζει στο element name
function getTime(name) {
    var myTime = new Date();

    var curTime=addZero(myTime.getHours())+':'+
                addZero(myTime.getMinutes())+':'+
                addZero(myTime.getSeconds());

    $(name).text(curTime);
}


// αναζήτηση στην playlist
function clearData(message) {
    var confirmAnswer=confirm(message);

    if (confirmAnswer==true) {
        $('#progress').show();

        days = $('#clearDays').val();

        callFile = "clearData.php?days=" + days;

        $.get(callFile, function ( data ) {

            $('#clearResponse').text(data.success).show().delay(5000).hide('slow');

            $('#progress').hide();

        }, "json" );
    }

}

$(function(){
    $('#LoginForm').validate({ // initialize the plugin
        errorElement: 'div'
    });

    $('#RegisterForm').validate({ // initialize the plugin
        errorElement: 'div',
             rules : {
                 repeat_password: {
                     equalTo : '[name="password"]'
                 }
             }
    });


    $('.users_form').each(function() {  // attach to all form elements on page
        $(this).validate({       // initialize plugin on each form
            errorElement: 'div'
        //     rules : {
        //         repeat_password: {
        //             equalTo : '[name="password"]'
        //         }
        //     }
        });
    });

    $('.alerts_form').each(function() {  // attach to all form elements on page
        $(this).validate({       // initialize plugin on each form
            errorElement: 'div'
        });
    });

    $('.options_form').each(function() {  // attach to all form elements on page
        $(this).validate({       // initialize plugin on each form
            errorElement: 'div'
        });
    });

    $('.powers_form').each(function() {  // attach to all form elements on page
        $(this).validate({       // initialize plugin on each form
            errorElement: 'div'
        });
    });

    $('.sensors_form').each(function() {  // attach to all form elements on page
        $(this).validate({       // initialize plugin on each form
            errorElement: 'div'
        });
    });





    getTime('#timetext'); // Εμφανίζει την ώρα

    // Load the Visualization API and the corechart package.
    google.charts.load('current', {'packages':['corechart']});


    // Κάνει τον συνεχή έλεγχο για την κατάσταση των αισθητήρων αφού πρώτα το εμφανίσει αμέσως
    checkIfMysqlIsAlive('#MysqlStatusText');
    setInterval(function(){
        checkIfMysqlIsAlive('#MysqlStatusText');

    }, IntervalValue*1000);


    setInterval(function(){  // Εμφανίζει συνεχώς την ώρα
        getTime('#timetext');

    }, 1000);


    // Έλεγχος αν το repeat password  συμφωνεί με το password
    $('.UsersList').find('input[name=repeat_password]').keyup(function () {
        curEl=eval($(document.activeElement).prop('id'));

        // console.log($('#password'+curEl).val());

        if ($('#password'+curEl).val() === $(this).val()) {
            $(this)[0].setCustomValidity('');

        } else {
            $(this)[0].setCustomValidity('Passwords must match');
        }

    });


    $('#RegisterForm').find('input[name=repeat_password]').keyup(function () {
        // curEl=eval($(document.activeElement).prop('id'));
        //
        // console.log($(this).val());

        if ($('input[name=password]').val() === $(this).val()) {
            $(this)[0].setCustomValidity('');

        } else {
            $(this)[0].setCustomValidity('Passwords must match');
        }

    });
    



});



