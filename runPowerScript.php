<?php
/**
 * File: runPowerScript.php
 * Created by rocean
 * Date: 11/10/16
 * Time: 22:45
 *
 * Τρέχει το script που αλλάζει τον διακόπτη και επιστρέφει το αποτέλεσμα
 */

// Καθαρίζει τα data που έδωσε ο χρήστης από περίεργο κώδικα
function ClearString($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}



if(isset($_GET['id']))
    $id=ClearString($_GET['id']);

if(isset($_GET['newStatus']))
    $newStatus=ClearString($_GET['newStatus']);

if(isset($_GET['macAddress']))
    $macAddress=ClearString($_GET['macAddress']);

// Παράδειγμα εκτέλεσης php script που περνάει το new status και την αντίστοιχη mac address του διακόπτη
//        $output = shell_exec('php ManageSwitch.php?newstatus='.$newStatus.'&macaddress='.urlencode($macAddress));
// επιστρέφει στο $output το αποτέλεσμα


// Στην επιτυχία επιστρέφει true
$jsonArray = array('success' => true);


// Στην αποτυχία επιστρέφει false
//$jsonArray = array('success' => false);


// στέλνει το αποτέλεσμα σαν JSON
echo json_encode($jsonArray);


?>