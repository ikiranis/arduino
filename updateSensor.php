<?php
/**
 * File: updateSensor.php
 * Created by rocean
 * Date: 26/05/16
 * Time: 21:47
 * Ενημέρωση εγγραφής στο sensors
 */


require_once('libraries/common.inc.php');

session_start();

if(isset($_GET['id']))
    $id=ClearString($_GET['id']);

if(isset($_GET['room']))
    $room=ClearString($_GET['room']);

if(isset($_GET['sensor_name']))
    $sensor_name=ClearString($_GET['sensor_name']);

if(isset($_GET['db_field']))
    $db_field=ClearString($_GET['db_field']);


$conn = new RoceanDB();
$conn->CreateConnection();

if ($id==0) {  // Αν το id είναι 0 τότε κάνει εισαγωγή
    $sql = 'INSERT INTO sensors (room, sensor_name, db_field) VALUES (?,?,?)';
    $SQLparams=array($room, $sensor_name, $db_field);
}

else {   // αλλιώς κάνει update
    $sql = 'UPDATE sensors SET room=?, sensor_name=?, db_field=? WHERE id=?';
    $SQLparams=array($room, $sensor_name, $db_field, $id);
}

$stmt = RoceanDB::$conn->prepare($sql);

if($stmt->execute($SQLparams)) {
    if($id==0) {
        $inserted_id=RoceanDB::$conn->lastInsertId();
        $jsonArray=array( 'success'=>'true', 'lastInserted'=>$inserted_id);

        RoceanDB::insertLog('Insert of new Sensor '.$room.':'.$sensor_name); // Προσθήκη της κίνησης στα logs
    }
    else {
        $jsonArray = array('success' => 'true');

        RoceanDB::insertLog('Sensor updated with id '.$id); // Προσθήκη της κίνησης στα logs
    }
        
}
else $jsonArray=array( 'success'=>'false');

echo json_encode($jsonArray);

$stmt->closeCursor();
$stmt = null;

?>