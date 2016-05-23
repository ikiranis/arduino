<?php
/**
 * File: getPowerStatus.php
 * Created by rocean
 * Date: 24/05/16
 * Time: 01:20
 */

require_once('libraries/common.inc.php');

if(isset($_GET['id']))
    $id=$_GET['id'];


$conn = new RoceanDB();
$conn->CreateConnection();

$sql = 'SELECT status FROM power WHERE id=?';
$stmt = RoceanDB::$conn->prepare($sql);

$stmt->execute(array($id));


if($item=$stmt->fetch(PDO::FETCH_ASSOC))
{


    echo json_encode( array( 'status'=>$item['status']));

}

?>