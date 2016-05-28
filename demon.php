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

require_once('libraries/common.inc.php');
require_once ('libraries/PHPMailer/PHPMailerAutoload.php');

function CheckForAlerts() {

    $conn = new RoceanDB();
    $mail = new PHPMailer;

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'rocean74test';                 // SMTP username
    $mail->Password = 'Wvi5$a$YPH#c';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    $mail->setFrom('rocean74@gmail.com', 'itbusiness');

    $alerts = $conn->getTableArray('alerts');

    $mail->isHTML(true);

    $lastTemperatures=Arduino::getOnlyLastTemperatures();


    foreach ($lastTemperatures as $temperature) {
        $sensorID=Arduino::getSensorID($temperature['sensor']);

        foreach ($alerts as $alert) {
            if($sensorID==$alert['sensors_id'])
                if($temperature['temp']>$alert['temp_limit']) {
                    // $alert['time_limit'] * 60 -> λεπτά, $alert['time_limit'] * 60 * 60 -> ώρες
                    $newtime = strtotime($alert['alert_time']) + ($alert['time_limit'] * 60 * 60);
                    if ($newtime < time()) {
                        $mailText = 'ALERT: ' . $temperature['sensor']
                            . ' έχει περάσει το όριο. Είναι: ' . $temperature['temp'];

                        Arduino::setTimeToAlert($alert['id']);

                        $mail->addAddress($alert['email'], 'rocean');
                        $mail->Subject = 'ALERT for Sensor '.$temperature['sensor'];
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




$counter=1;

do {     // loop του demon. Τρέχει στο crontab ανά ένα λεπτό. Οπότε οι ενδιάμεσοι έλεγχοι γίνονται με αυτό το loop
    CheckForAlerts();
    $counter++;
    sleep(INTERVAL_VALUE);
} while ($counter<((60/INTERVAL_VALUE)+1));    // Αν το INTERVAL_VALUE είναι 5 τότε εκτελείται 12 φορές το λεπτό



?>