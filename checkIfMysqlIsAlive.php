<?php
/**
 * File: checkIfMysqlIsAlive.php
 * Created by rocean
 * Date: 26/05/16
 * Time: 17:32
 * Έλεγχος αν η mysql συνεχίζει να δέχεται τιμές. Επιστρέφει true or false
 */

require_once('libraries/common.inc.php');




$conn = new RoceanDB();
$conn->CreateConnection();

$sql = 'SELECT UNIX_TIMESTAMP(time) FROM data ORDER BY time DESC';
$stmt = RoceanDB::$conn->prepare($sql);

$stmt->execute();



if($item=$stmt->fetch(PDO::FETCH_ASSOC))
{
    $diff= time()-$item['UNIX_TIMESTAMP(time)']; // Διαφορά της τρέχουσας ώρας με την ώρα της τελευταίας εγγραφής

    // Αν η διαφορά είναι μικρότερη από το 10 τότε η mysql είναι ζωντανή (true), αλλιώς false
    if($diff<intval((INTERVAL_VALUE*2)-(INTERVAL_VALUE/2)))
        $DBStatus=true;
    else $DBStatus=false;

    echo json_encode(array('DBStatus'=>$DBStatus));

}

?>