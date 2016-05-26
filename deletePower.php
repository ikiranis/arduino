<?php
/**
 * File: deletePower.php
 * Created by rocean
 * Date: 26/05/16
 * Time: 23:53
 */

require_once('libraries/common.inc.php');

if(isset($_GET['id']))
    $id=ClearString($_GET['id']);


$conn = new RoceanDB();
$conn->CreateConnection();


$sql = 'DELETE FROM power WHERE id=?';

$stmt = RoceanDB::$conn->prepare($sql);

if($stmt->execute(array($id))) {
    $jsonArray=array( 'success'=>'true');

}
else $jsonArray=array( 'success'=>'false');

echo json_encode($jsonArray);

?>