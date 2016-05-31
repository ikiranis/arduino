<?php
/**
 * File: getStatistics.php
 * Created by rocean
 * Date: 31/05/16
 * Time: 17:22
 * Επιστρέφει τον πίνακα με τα data για εμφάνιση σε graph
 */


require_once('libraries/common.inc.php');

if(isset($_GET['db_field']))
    $db_field=ClearString($_GET['db_field']);

if(isset($_GET['date_limit']))
    $date_limit=ClearString($_GET['date_limit']);


$conn = new RoceanDB();


$conn->CreateConnection();


switch ($date_limit) {
    case 1: $sql = 'SELECT time,'.$db_field.' FROM data ORDER BY time DESC LIMIT 100'; break;
}


$stmt = RoceanDB::$conn->prepare($sql);

$stmt->execute();

$times=array();
$temps=array();

$counter=0;

while($item=$stmt->fetch(PDO::FETCH_ASSOC))
{


        $temps=$temps+array('temp'.$counter=>$item[$db_field]);
        $times=$times+array('time'.$counter=>$item['time']);
        $counter++;



}



echo json_encode(array('times'=>$times, 'temps'=>$temps));    // στέλνει το array σε json στην javascript


?>