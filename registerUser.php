<?php
/**
 * File: registerUser.php
 * Created by rocean
 * Date: 08/06/16
 * Time: 17:35
 * Εισαγωγή αρχικού χρήστη
 */



require_once('libraries/common.inc.php');


$conn = new RoceanDB();
$lang = new Language();

if(isset($_GET['username']))
    $username=ClearString($_GET['username']);

if(isset($_GET['password']))
    $password=ClearString($_GET['password']);

if (isset($_GET['email']))
    $email=$_GET['email'];



$register=$conn->CreateUser($username, $email, $password, '1', 'local', null, null);

if($register['success']) {
    $jsonArray=array( 'success'=>true);
}
else {
    $jsonArray=array( 'success'=>false);
}


$dbstatus=$conn->getOption('dbstatus');

if(!$dbstatus)
    $conn->createOption('dbstatus','off',0);


echo json_encode($jsonArray, JSON_UNESCAPED_UNICODE);

?>