<?php
/**
 * File: updatePowerStatus.php
 * Created by rocean
 * Date: 24/05/16
 * Time: 01:36
 */


require_once('libraries/common.inc.php');

if(isset($_GET['id']))
    $id=ClearString($_GET['id']);

$conn=new RoceanDB();

$oldStatus=Arduino::getPowerStatus($id);
$macAddress=Arduino::getPowerMac($id);
$dbstatus=$conn->getOption('dbstatus');


if($dbstatus=='on') {

    if ($oldStatus == 'ON') $newStatus = 'OFF';
    else $newStatus = 'ON';


    if (Arduino::setPowerStatus($id, $newStatus)) {
        // TODO να ενεργοποιηθεί ο κώδικας του τρεξίματος του script
        // Arduino::runPowerScript($id,$newStatus,$macAddress);
        echo json_encode(array('success' => 'true', 'status' => $newStatus));
    } else echo json_encode(array('success' => 'false'));

} else echo json_encode(array('success' => 'false'));



?>