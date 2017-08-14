<?php
/**
 *
 * File: insertTemp.php
 *
 * Created by Yiannis Kiranis <rocean74@gmail.com>
 * http://www.apps4net.eu
 *
 * Date: 15/06/2017
 * Time: 19:38
 *
 * Εισαγωγή θερμοκρασίας
 *
 */

require_once('libraries/common.inc.php');

session_start();

if(isset($_GET['sensorName'])) {
    $probe_name=ClearString($_GET['sensorName']);
}


if(isset($_GET['temp']))
    $temp=ClearString($_GET['temp']);

if(isset($_GET['hum']))
    $hum=ClearString($_GET['hum']);


$conn = new RoceanDB();
$conn->CreateConnection();

$DateTime = date('Y-m-d H:i:s');

$sql = 'INSERT INTO temporary_temps (probe_name, temp, my_date) VALUES(?,?,?)';
$stmt = RoceanDB::$conn->prepare($sql);

$ArrayValues = array ($probe_name, $temp, $DateTime);

if($stmt->execute($ArrayValues)) {
    echo 'Done';
//    trigger_error('DONE');
} else {
//    trigger_error('PROBLEM');
    echo 'Problem';
}

$sensors = $conn->getTableArray('sensors','*', 'NOT room=?', array('disable'), null);
$sensorsNumber = count($sensors);

$temporary_temps = $conn->getTableArray('temporary_temps','*', null, null, 'my_date DESC LIMIT ' . $sensorsNumber);   // Παίρνει τα δεδομένα του πίνακα sensors σε array

if(count($temporary_temps)==$sensorsNumber) {
    $sql = 'INSERT INTO data (time, probe1, probe2, probe3, probe4, probe5, probeCPU) VALUES(?,?,?,?,?,?,?)';
    $stmt = RoceanDB::$conn->prepare($sql);

    $probes = array(0,0,0,0,0,0);

    for($i=0; $i<$sensorsNumber; $i++) {
        $probeKey = array_search('probe'.($i+1), array_column($temporary_temps, 'probe_name'));
        if(false!==$probeKey) {
            $probes[$i] = $temporary_temps[$probeKey]['temp'];
        }
    }

    $ArrayValues = array ($DateTime,$probes[0],$probes[1],$probes[2],$probes[3],$probes[4],$probes[5]);

    if($stmt->execute($ArrayValues)) {
        echo 'Done';
        RoceanDB::clearTheTable('temporary_temps');
//    trigger_error('DONE');
    } else {
//    trigger_error('PROBLEM');
        echo 'Problem';
    }

}




