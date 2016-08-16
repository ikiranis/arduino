<?php
/**
 * File: clearData.php
 * Created by rocean
 * Date: 16/08/16
 * Time: 20:06
 * Καθαρίζει τον πίνακα data από παλιότερες εγγραφές
 */


require_once('libraries/common.inc.php');

session_start();

if(isset($_GET['days']))
    $days=ClearString($_GET['days']);

    if(RoceanDB::deleteTableBeforeNDays('data', 'time', $days)){
        $jsonArray=array( 'success'=>'Η διαγραφή έγινε');
        RoceanDB::insertLog('Deleted data before '.$days.' days');  // Προσθήκη της κίνησης στα logs
    }

    else {
        $jsonArray = array('success' => 'Η διαγραφή δεν ήταν επιτυχής');
    }

echo json_encode($jsonArray, JSON_UNESCAPED_UNICODE);


