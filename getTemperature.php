<?php
/**
 * File: getTemperature.php
 * Created by rocean
 * Date: 23/05/16
 * Time: 01:29
 */

require_once('libraries/common.inc.php');


$FieldName=$_GET['field'];


$conn = new RoceanDB();
$conn->CreateConnection();

$sql = 'SELECT '.$FieldName.',time FROM data ORDER BY time DESC';
$stmt = RoceanDB::$conn->prepare($sql);

$stmt->execute();


if($item=$stmt->fetch(PDO::FETCH_ASSOC))
{
//    echo '<span id=value1>'.$item[$FieldName].'</span>';
//    echo '<span id=value2>'.$item['time'].'</span>';

    echo json_encode( array( "fieldname"=>$item[$FieldName],
                "time"=>$item['time'] ) );

}

?>