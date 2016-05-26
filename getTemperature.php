<?php
/**
 * File: getTemperature.php
 * Created by rocean
 * Date: 23/05/16
 * Time: 01:29
 */

require_once('libraries/common.inc.php');




$conn = new RoceanDB();
$conn->CreateConnection();

$sql = 'SELECT * FROM data ORDER BY time DESC';
$stmt = RoceanDB::$conn->prepare($sql);

$stmt->execute();


$jsonArray=array();


if($item=$stmt->fetch(PDO::FETCH_ASSOC))
{
    $counter=1;
    foreach ($sensorsArray as $sensor) {
        $jsonArray=$jsonArray+array('probe'.$counter=>$item[$sensor['db_field']]);
        $counter++;
    }
    $jsonArray=$jsonArray+array("time"=>$item['time']);

    echo json_encode($jsonArray);


}

?>