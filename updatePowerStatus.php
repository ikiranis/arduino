<?php
/**
 * File: updatePowerStatus.php
 * Created by rocean
 * Date: 24/05/16
 * Time: 01:36
 */


require_once('libraries/common.inc.php');

session_start();

if(isset($_GET['id']))
    $id=ClearString($_GET['id']);

$conn=new RoceanDB();

$oldStatus=Arduino::getPowerStatus($id);
$macAddress=Arduino::getPowerMac($id);
$dbstatus=$conn->getOption('dbstatus');

if($dbstatus=='on') {

    if ($oldStatus == 'ON') $newStatus = 'OFF';
    else $newStatus = 'ON';

    // Κάνει την εισαγωγή στην βάση
    if (Arduino::setPowerStatus($id, $newStatus)) {  // Κάνει την αλλαγή στην βάση
        echo json_encode(array('success' => 'true', 'status' => $newStatus, 'relayIP' => $macAddress ));

        RoceanDB::insertLog('Switcher ' . Arduino::getPowerName($id) . ' to ' . $newStatus);  // Προσθήκη της κίνησης στα logs
    } else { // Αν η αλλαγή στην βάση δεν είναι επιτυχής, επαναφέρει και τον διακόπτη στην παλιά θέση
        echo json_encode(array('success' => 'false'));
        Arduino::runPowerScript($id,$oldStatus,$macAddress); // Επαναφορά του διακόπτη στην παλιά θέση αφού απέτυχε να γίνει η αλλαγή στην βάση
    }

    // τρέχει το κατάλληλο script για να ανοιγοκλείσει ο διακόπτης
//    Arduino::runPowerScript($id,$newStatus,$macAddress);

} else {
    echo json_encode(array('success' => 'false'));
}
