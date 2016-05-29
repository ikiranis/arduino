<?php
/**
 * File: getTemperature.php
 * Created by rocean
 * Date: 23/05/16
 * Time: 01:29
 */

require_once('libraries/common.inc.php');


$LastTemps=Arduino::getLastTemperatures();
$AvgTemps=Arduino::getAvgLastTemperatures();


    echo json_encode(array('LastTemps'=>$LastTemps,'AvgTemps'=>$AvgTemps)); // στέλνει το array σε json στην javascript



?>