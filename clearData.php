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

set_time_limit(0);

$lang = new Language();

if(isset($_GET['days']))
    $days=ClearString($_GET['days']);

    if(RoceanDB::deleteTableBeforeNDays('data', 'time', $days)){
        $jsonArray=array( 'success'=> __('options_clear_data_response_success') );
        RoceanDB::insertLog('Deleted data before '.$days.' days');  // Προσθήκη της κίνησης στα logs
    }

    else {
        $jsonArray = array('success' => __('options_clear_data_response_fail') );
    }

echo json_encode($jsonArray, JSON_UNESCAPED_UNICODE);


