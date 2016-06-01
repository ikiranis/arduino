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
else $db_field='probe1';

if(isset($_GET['date_limit']))
    $date_limit=ClearString($_GET['date_limit']);
else $date_limit=1;


$conn = new RoceanDB();


$conn->CreateConnection();


switch ($date_limit) {
    case 1: $sql = 'SELECT max(time) as mytime, avg('.$db_field.') as avg 
    FROM data where time>= DATE_SUB(NOW(),INTERVAL 1 HOUR) 
    GROUP BY minute(time) ORDER BY minute(time) DESC'; break;  // Τελευταία ώρα σε λεπτά

    case 2: $sql = 'SELECT max(time) as mytime, avg('.$db_field.') as avg 
    FROM data where time>= DATE_SUB(NOW(),INTERVAL 1 DAY) 
    GROUP BY hour(time) ORDER BY hour(time) DESC'; break;   // Τελευταία μέρα σε ώρες

    case 3: $sql = 'SELECT max(time) as mytime, avg('.$db_field.') as avg 
    FROM data where time>= DATE_SUB(NOW(),INTERVAL 1 WEEK) 
    GROUP BY day(time) ORDER BY day(time) DESC'; break;   // Τελευταία βδομάδα σε μέρες

    case 4: $sql = 'SELECT max(time) as mytime, avg('.$db_field.') as avg 
    FROM data where time>= DATE_SUB(NOW(),INTERVAL 1 month) 
    GROUP BY day(time) ORDER BY day(time) DESC'; break;   // Τελευταίος μήνας σε μέρες

    case 5: $sql = 'SELECT max(time) as mytime, avg('.$db_field.') as avg 
    FROM data where time>= DATE_SUB(NOW(),INTERVAL 1 year) 
    GROUP BY month(time) ORDER BY month(time) DESC'; break;   // Τελευταίο έτος σε μήνες

    case 6: $sql = 'SELECT max(time) as mytime, avg('.$db_field.') as avg 
    FROM data  
    GROUP BY month(time) ORDER BY month(time) DESC'; break;   // Από την αρχή σε μήνες
}


$stmt = RoceanDB::$conn->prepare($sql);

$stmt->execute();

$times=array();
$temps=array();

$counter=0;

while($item=$stmt->fetch(PDO::FETCH_ASSOC))
{


        $temps=$temps+array('temp'.$counter=>$item['avg']);
        $times=$times+array('time'.$counter=>$item['mytime']);
        $counter++;



}



echo json_encode(array('times'=>$times, 'temps'=>$temps));    // στέλνει το array σε json στην javascript

$stmt->closeCursor();
$stmt = null;


?>