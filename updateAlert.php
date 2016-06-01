<?php
/**
 * File: updateAlert.php
 * Created by rocean
 * Date: 28/05/16
 * Time: 20:08
 * Ενημέρωση ή εισαγωγή στον πίνακα alerts
 */


require_once('libraries/common.inc.php');

if(isset($_GET['id']))
    $id=ClearString($_GET['id']);

if(isset($_GET['email']))
    $email=ClearString($_GET['email']);

if(isset($_GET['time_limit']))
    $time_limit=ClearString($_GET['time_limit']);

if(isset($_GET['temp_limit']))
    $temp_limit=ClearString($_GET['temp_limit']);

if(isset($_GET['sensors_id']))
    $sensors_id=ClearString($_GET['sensors_id']);

if(isset($_GET['user_id']))
    $user_id=ClearString($_GET['user_id']);


$conn = new RoceanDB();
$conn->CreateConnection();

if ($id==0) {  // Αν το id είναι 0 τότε κάνει εισαγωγή
    $sql = 'INSERT INTO alerts (email, time_limit, temp_limit, sensors_id, user_id) VALUES (?,?,?,?,?)';
    $SQLparams=array($email, $time_limit, $temp_limit, $sensors_id, $user_id);
}

else {   // αλλιώς κάνει update
    $sql = 'UPDATE alerts SET email=?, time_limit=?, temp_limit=?, sensors_id=?, user_id=? WHERE id=?';
    $SQLparams=array($email, $time_limit, $temp_limit, $sensors_id, $user_id, $id);
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