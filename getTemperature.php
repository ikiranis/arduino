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


if($item=$stmt->fetch(PDO::FETCH_ASSOC))
{


    echo json_encode( array( "probe1"=>$item['probe1'], "probe2"=>$item['probe2'],
        "probe3"=>$item['probe3'],"probe4"=>$item['probe4'],"probe5"=>$item['probe5'],"probeCPU"=>$item['probeCPU'],
                "time"=>$item['time'] ) );

}

?>