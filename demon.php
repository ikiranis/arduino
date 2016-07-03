<?php
/**
 * File: demon.php
 * Created by rocean
 * Date: 27/05/16
 * Time: 19:44
 * Συνεχής έλεγχος για την κατάσταση διάφορων πραγμάτων για alerts κτλ
 * Τρέχει συνεχώς στο crontab
 * PHP Mailer https://github.com/PHPMailer/PHPMailer
 */



require_once ('libraries/common.inc.php');
require_once ('libraries/PHPMailer/PHPMailerAutoload.php');

$conn = new RoceanDB();
$mail = new PHPMailer;




function CheckForAlerts() {

    global $conn;
    global $mail;


    $alerts = $conn->getTableArray('alerts');


    $lastTemperatures=Arduino::getOnlyLastTemperatures();


    foreach ($lastTemperatures as $temperature) {
        $sensorID=Arduino::getSensorID($temperature['sensor']);

        foreach ($alerts as $alert) {
            if($sensorID==$alert['sensors_id'])
                if($temperature['temp']>$alert['temp_limit']) {
                    // $alert['time_limit'] * 60 -> λεπτά, $alert['time_limit'] * 60 * 60 -> ώρες
                    $newtime = strtotime($alert['alert_time']) + ($alert['time_limit'] * 60 * 60);
                    if ($newtime < time()) {
                        $mail->isSMTP();                                      // Set mailer to use SMTP
                        $mail->CharSet = 'UTF-8';
                        $mail->Host = $conn->getOption('mail_host');  // Specify main and backup SMTP servers
                        $mail->SMTPAuth = true;                               // Enable SMTP authentication
                        $mail->Username = $conn->getOption('mail_username');                 // SMTP username
                        $mail->Password = $conn->getOption('mail_password');                           // SMTP password
                        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
                        $mail->Port = 587;                                    // TCP port to connect to

                        $mail->setFrom($conn->getOption('mail_from'), $conn->getOption('mail_from_name'));

                        $mail->isHTML(true);

                        $sensors=$conn->getTableArray('sensors');   // Παίρνει τα δεδομένα του πίνακα sensors σε array

                        $key = array_search($alert['sensors_id'], array_column($sensors, 'id'));


                        $mailText = 'Sensor: '. $sensors[$key]['room'] . ':' . $sensors[$key]['sensor_name']. '<br>'
                            . 'Temperature: ' . $temperature['temp'];

                        Arduino::setTimeToAlert($alert['id']);

                        $mail->addAddress($alert['email'], 'user');
                        $mail->Subject = 'ALERT for Sensor '.$sensors[$key]['room'] . ':' . $sensors[$key]['sensor_name'];
                        $mail->Body    = $mailText;

                        if(!$mail->send()) {
                            echo 'Message could not be sent.<br>';
                            echo 'Mailer Error: ' . $mail->ErrorInfo.'<br>';
                        } else {
                            echo 'Message has been sent<br>';
                        }
                    }
                    else echo 'Πέρασε το όριο, αλλά έχει γίνει ήδη alert <br>';
                }

        }

    }



}


function CheckForMysqlAlive() {
    global $conn;

    $sql = 'SELECT UNIX_TIMESTAMP(time) FROM data ORDER BY time DESC LIMIT 1';
    $stmt = RoceanDB::$conn->prepare($sql);

    $stmt->execute();

    if($item=$stmt->fetch(PDO::FETCH_ASSOC))
    {
        $diff= time()-$item['UNIX_TIMESTAMP(time)']; // Διαφορά της τρέχουσας ώρας με την ώρα της τελευταίας εγγραφής
        
        $lastDBStatus=$conn->getOption('dbstatus');  // Παίρνει την τρέχουσα κατάσταση του συστήματος
        

        // Αν η διαφορά είναι μικρότερη από το 10 τότε η mysql είναι ζωντανή (true), αλλιώς false
        if($diff<intval((INTERVAL_VALUE*2)-(INTERVAL_VALUE/2))+1){
            if($lastDBStatus=='off') { // Αν ήταν off η προηγούμενη κατάσταση τότε να τρέξει κώδικα που επαναφέρει τους διακόπτες
                Arduino::putSwitchesToPreviousPosition();

            }

            $conn->changeOption('dbstatus', 'on');
                
        }
        
        else {
            $conn->changeOption('dbstatus', 'off');
        }


    }

    $stmt->closeCursor();
    $stmt = null;
}



$counter=1;

do {     // loop του demon. Τρέχει στο crontab ανά ένα λεπτό. Οπότε οι ενδιάμεσοι έλεγχοι γίνονται με αυτό το loop
    CheckForAlerts();
    CheckForMysqlAlive();
    $counter++;
    sleep(INTERVAL_VALUE);
} while ($counter<((60/INTERVAL_VALUE))+1);    // Αν το INTERVAL_VALUE είναι 5 τότε εκτελείται 12 φορές το λεπτό

$conn = null;

?>