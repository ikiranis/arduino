<?php
/**
 * File: getDBstatus.php
 * Created by rocean
 * Date: 09/06/16
 * Time: 02:03
 * Παίρνει την τιμή του dbstatus
 */

require_once('libraries/common.inc.php');

$conn = new RoceanDB();

$dbstatus=$conn->getOption('dbstatus');

$jsonArray=array( 'DBStatus'=>$dbstatus);

echo json_encode($jsonArray, JSON_UNESCAPED_UNICODE);

