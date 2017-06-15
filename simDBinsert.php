<?php
/**
 * File: simDBinsert.php
 * Created by rocean
 * Date: 23/05/16
 * Time: 00:18
 * Simulation της εισαγωγής θερμοκρασιών στην βάση
 */


require_once ("libraries/common.inc.php");

$conn = new RoceanDB();
$conn->CreateConnection();

$sql = 'INSERT INTO data (time, probe1, probe2, probe3, probe4, probe5, probeCPU) VALUES(?,?,?,?,?,?,?)';
$stmt = RoceanDB::$conn->prepare($sql);

$counter=1;

if(RoceanDB::countTable('data')==0) {
    $DateTime = date('Y-m-d H:i:s');

    $ArrayValues= array ($DateTime,20,20,20,20,20,20);

    $stmt->execute($ArrayValues);
}




do {
    $lastTemperatures=Arduino::getOnlyLastTemperatures();


    $sensors=array();

    $DateTime = date('Y-m-d H:i:s');

    $i=0;

    foreach ($lastTemperatures as $temperature) {
        $someRandomNumber=rand(1,12);


        $lastTemp=$temperature['temp'];

        echo $lastTemp.' ';

        switch ($someRandomNumber) {
            case 1: $newTemp=$lastTemp+1; break;
            case 2: $newTemp=$lastTemp-1; break;
            case 3: $newTemp=$lastTemp; break;
            case 4: $newTemp=$lastTemp; break;
            case 5: $newTemp=$lastTemp; break;
            case 6: $newTemp=$lastTemp; break;
            case 7: $newTemp=$lastTemp; break;
            case 8: $newTemp=$lastTemp; break;
            case 9: $newTemp=$lastTemp; break;
            case 10: $newTemp=$lastTemp; break;
            case 11: $newTemp=$lastTemp; break;
            case 12: $newTemp=$lastTemp; break;


        }

        $sensors[$i]=$newTemp;

        $i++;

    }

    echo '<br>';



    $ArrayValues= array ($DateTime,$sensors[0],$sensors[1],$sensors[2],$sensors[3],$sensors[4],$sensors[5]);



    $stmt->execute($ArrayValues);
    $counter++;
    sleep(5);
} while ($counter<13);

$stmt->closeCursor();
$stmt = null;
$conn = null;

?>