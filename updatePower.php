<?php
/**
 * File: updatePower.php
 * Created by rocean
 * Date: 26/05/16
 * Time: 22:22
 */


require_once('libraries/common.inc.php');

if(isset($_GET['id']))
    $id=ClearString($_GET['id']);

if(isset($_GET['room']))
    $room=ClearString($_GET['room']);

if(isset($_GET['power_name']))
    $power_name=ClearString($_GET['power_name']);

$conn = new RoceanDB();
$conn->CreateConnection();

if ($id==0) {  // Αν το id είναι 0 τότε κάνει εισαγωγή
    $sql = 'INSERT INTO power (room, power_name,status) VALUES (?,?,?)';
    $SQLparams=array($room, $power_name,'OFF');
}

else {   // αλλιώς κάνει update
    $sql = 'UPDATE power SET room=?, power_name=? WHERE id=?';
    $SQLparams=array($room, $power_name, $id);
}

$stmt = RoceanDB::$conn->prepare($sql);

if($stmt->execute($SQLparams)) {
    if($id==0) {
        $inserted_id=RoceanDB::$conn->lastInsertId();
        $jsonArray=array( 'success'=>'true', 'lastInserted'=>$inserted_id);
    }
    else $jsonArray=array( 'success'=>'true');

}
else $jsonArray=array( 'success'=>'false');

echo json_encode($jsonArray);

$stmt->closeCursor();
$stmt = null;

?>