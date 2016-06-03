<?php
/**
 * File: updateUser.php
 * Created by rocean
 * Date: 03/06/16
 * Time: 18:25
 * Ενημερώνει μια εγγραφή στους users ή κάνει νέα εγγραφή
 */


require_once('libraries/common.inc.php');

if(isset($_GET['id']))
    $id=ClearString($_GET['id']);

if(isset($_GET['username']))
    $username=ClearString($_GET['username']);

if(isset($_GET['email']))
    $email=ClearString($_GET['email']);

if(isset($_GET['password']))
    $password=ClearString($_GET['password']);
else $password=null;

if(isset($_GET['usergroup']))
    $usergroup=ClearString($_GET['usergroup']);

if(isset($_GET['fname']))
    $fname=ClearString($_GET['fname']);
else $fname='';

if(isset($_GET['lname']))
    $lname=ClearString($_GET['lname']);
else $lname='';

$conn = new RoceanDB();
$conn->CreateConnection();

if ($id==0) {  // Αν το id είναι 0 τότε κάνει εισαγωγή
    if($inserted_id=$conn->CreateUser($username, $email, $password, $usergroup, 'local', $fname, $lname)) // Δημιουργεί τον χρήστη
        $jsonArray=array( 'success'=>'true', 'lastInserted'=>$inserted_id);
    else $jsonArray=array( 'success'=>'false');
}

else {   // αλλιώς κάνει update
    $result=$conn->UpdateUser($id, $username, $email, $password, $usergroup, 'local', $fname, $lname); // Ενημερώνει την εγγραφή
    
    $jsonArray=array( 'success'=>$result);

}

echo json_encode($jsonArray);



?>