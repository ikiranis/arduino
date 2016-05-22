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


do {
    $DateTime = date('Y-m-d H:i:s');
    $ArrayValues= array ($DateTime,rand(10,50),rand(10,50),rand(10,50),rand(10,50),rand(10,50),rand(10,50));
    var_dump($ArrayValues);
    $stmt->execute($ArrayValues);
    $counter++;
    sleep(5);
} while ($counter<50);