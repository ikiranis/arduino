<?php

/**
 * File: login.php
 * Created by rocean
 * Date: 17/04/16
 * Time: 01:17
 */

require_once ('libraries/common.inc.php');

session_start();

$lang = new Language();



if (isset($_POST['submit'])) {

    if (isset($_POST['SavePassword']))
        $SavePassword=true;
    else $SavePassword=false;

    $myConnect = new RoceanDB();
    $login=$myConnect->CheckLogin(ClearString($_POST['username']), ClearString($_POST['password']), $SavePassword);
    if($login['success']) {
        echo $login['message'];
        header('Refresh:3;URL=index.php');
    }
    else {
        echo $login['message'];
        header('Refresh:3;URL=index.php');
    }


}

if (isset($_POST['register'])) {
    
    $conn = new RoceanDB();

    // Έλεγχος αν συμφωνούν τα 2 passwords
    if($_POST['password']==$_POST['repeat_password']) {
        if($conn->CreateUser(ClearString($_POST['username']), ClearString($_POST['email']), ClearString($_POST['password']), 'local')) // Δημιουργεί τον χρήστη
            echo '<p>'.__('register_with_success').'</p>';
    }
    else echo '<p>'.__('not_the_same_password').'</p>';
    


}




// Εμφάνιση επιλογών login
function showLoginWindow()
{

    $LoginWindow = new Page();



    ?>

    <div id="LoginWindow">


        <?php


        $FormElementsArray = array(
            array('name' => 'username',
                'fieldtext' => __('form_user_name'),
                'type' => 'text',
                'value' => null),
            array('name' => 'password',
                'fieldtext' => __('form_password'),
                'type' => 'password',
                'value' => null),
            array('name' => 'SavePassword',
                'fieldtext' => __('form_save_password'),
                'type' => 'checkbox',
                'value' => 'yes'),
            array('name' => 'submit',
                'fieldtext' => '',
                'type' => 'submit',
                'value' => __('form_login'))
        );

        $LoginWindow->MakeForm('login.php', $FormElementsArray);


        ?>

    </div>


    <?php


}

function ShowRegisterUser()
{
    $RegisterUserWindow = new Page();


    ?>

    <div id="RegisterUserWindow">


        <?php


        $FormElementsArray = array(
            array('name' => 'username',
                'fieldtext' => __('form_user_name'),
                'type' => 'text'),
            array('name' => 'email',
                'fieldtext' => __('form_email'),
                'type' => 'text',
                'value' => null),
            array('name' => 'password',
                'fieldtext' => __('form_password'),
                'type' => 'password',
                'value' => null),
            array('name' => 'repeat_password',
                'fieldtext' => __('form_repeat_password'),
                'type' => 'password',
                'value' => null),
            array('name' => 'register',
                'fieldtext' => '',
                'type' => 'submit',
                'value' => __('form_register'))
        );

        $RegisterUserWindow->MakeForm('login.php', $FormElementsArray);


        ?>

    </div>


    <?php


}

function DisplayUsers ()
{

    $conn = new RoceanDB();
    $conn->CreateConnection();
    
    $sql = 'SELECT * FROM user';
    
    $stmt = RoceanDB::$conn->prepare($sql);
    
    $stmt->execute();
    
    ?>
        <div id="UsersList">

                <div class="row">
                    <div class="userID">ID</div>
                    <div class="username">Username</div>
                    <div class="email">email</div>
                </div><br>

    
    <?php
    
    while($row=$stmt->fetch(PDO::FETCH_ASSOC))
    {
    ?>

            <div class="row">
                <div class="userID"><?php echo $row['user_id']; ?></div>
                <div class="username"><?php echo $row['username']; ?></div>
                <div class="email"><?php echo $row['email']; ?></div>
            </div><br>

        </div>
    <?php
    }


}
