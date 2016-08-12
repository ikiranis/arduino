<?php
/**
 * File: deleteAlert.php
 * Created by rocean
 * Date: 28/05/16
 * Time: 21:08
 * Σβήνει εγγραφή από τον πίνακα alerts
 */


require_once('libraries/common.inc.php');

session_start();

if(isset($_GET['id']))
    $id=ClearString($_GET['id']);


$conn = new RoceanDB();
$conn->CreateConnection();


$sql = 'DELETE FROM alerts WHERE id=?';

$stmt = RoceanDB::$conn->prepare($sql);

if($stmt->execute(array($id))) {
    $jsonArray=array( 'success'=>'true');

    RoceanDB::insertLog('Alert deleted with id '. $id); // Προσθήκη της κίνησης στα logs 

}
else $jsonArray=array( 'success'=>'false');

echo json_encode($jsonArray);


$stmt->closeCursor();
$stmt = null;

?>