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

if(isset($_GET['probe']))
    $probe=ClearString($_GET['probe']);

if(isset($_GET['temp']))
    $temp=ClearString($_GET['temp']);


$conn = new RoceanDB();
$conn->CreateConnection();

$sql = 'INSERT INTO data (time, probe1, probe2, probe3, probe4, probe5, probeCPU) VALUES(?,?,?,?,?,?,?)';
$stmt = RoceanDB::$conn->prepare($sql);

$DateTime = date('Y-m-d H:i:s');

$ArrayValues= array ($DateTime,$probe,0,0,0,0,0);

$stmt->execute($ArrayValues);
