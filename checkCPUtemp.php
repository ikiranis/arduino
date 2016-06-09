<?php
/**
 * File: checkCPUtemp.php
 * Created by rocean
 * Date: 10/06/16
 * Time: 00:12
 * Παίρνει την τελευταία θερμοκρασία της cpu
 */

require_once('libraries/common.inc.php');

$conn = new RoceanDB();

$lastCPUTemp=Arduino::getLastCPUTemp(CPU_FIELD);

$jsonArray=array( 'lastCPUtemp'=>$lastCPUTemp);

echo json_encode($jsonArray, JSON_UNESCAPED_UNICODE);