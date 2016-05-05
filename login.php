<?php

/**
 * File: login.php
 * Created by rocean
 * Date: 17/04/16
 * Time: 01:17
 */

require_once ('libraries/common.inc.php');

session_start();


if (isset($_POST['submit'])) {

    if (isset($_POST['SavePassword']))
        $SavePassword=true;
    else $SavePassword=false;

    $myConnect = new RoceanDB();
    $myConnect->CheckLogin($_POST['username'], $_POST['password'], $SavePassword);


}

if (isset($_POST['register'])) {
    
    $conn = new RoceanDB();
    
    $conn->CreateUser($_POST['username'], $_POST['email'], $_POST['password'], 'local');

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
                'fieldtext' => 'Username: ',
                'type' => 'text',
                'value' => null),
            array('name' => 'password',
                'fieldtext' => 'Password: ',
                'type' => 'password',
                'value' => null),
            array('name' => 'SavePassword',
                'fieldtext' => 'Remember Me',
                'type' => 'checkbox',
                'value' => 'yes'),
            array('name' => 'submit',
                'fieldtext' => '',
                'type' => 'submit',
                'value' => 'Login')
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
                'fieldtext' => 'Username: ',
                'type' => 'text'),
            array('name' => 'email',
                'fieldtext' => 'E-mail: ',
                'type' => 'text',
                'value' => null),
            array('name' => 'password',
                'fieldtext' => 'Password: ',
                'type' => 'password',
                'value' => null),
            array('name' => 'repeat_password',
                'fieldtext' => 'Repeat Password: ',
                'type' => 'password',
                'value' => null),
            array('name' => 'register',
                'fieldtext' => '',
                'type' => 'submit',
                'value' => 'Εγγραφή')
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
