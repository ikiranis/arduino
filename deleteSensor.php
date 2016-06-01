<?php
/**
 * File: deleteSensor.php
 * Created by rocean
 * Date: 26/05/16
 * Time: 23:43
 */


require_once('libraries/common.inc.php');

if(isset($_GET['id']))
    $id=ClearString($_GET['id']);


$conn = new RoceanDB();
$conn->CreateConnection();


$sql = 'DELETE FROM sensors WHERE id=?';

$stmt = RoceanDB::$conn->prepare($sql);

if($stmt->execute(array($id))) {
     $jsonArray=array( 'success'=>'true');

}
else $jsonArray=array( 'success'=>'false');

echo json_encode($jsonArray);

$stmt->closeCursor();
$stmt = null;

?>