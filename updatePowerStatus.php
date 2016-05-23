<?php
/**
 * File: updatePowerStatus.php
 * Created by rocean
 * Date: 24/05/16
 * Time: 01:36
 */


require_once('libraries/common.inc.php');

if(isset($_GET['id']))
    $id=$_GET['id'];


$conn = new RoceanDB();
$conn->CreateConnection();

$oldStatus=Arduino::getPowerStatus($id);

if($oldStatus=='ON') $newStatus='OFF';
else $newStatus='ON';

$sql = 'UPDATE power SET status=? WHERE id=?';
$stmt = RoceanDB::$conn->prepare($sql);

if($stmt->execute(array($newStatus, $id)))
    echo json_encode( array( 'success'=>'true'));



?>