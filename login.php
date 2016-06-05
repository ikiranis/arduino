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



//if (isset($_POST['submit'])) {
//
//    if (isset($_POST['SavePassword']))
//        $SavePassword=true;
//    else $SavePassword=false;
//
//    $myConnect = new RoceanDB();
//    $login=$myConnect->CheckLogin(ClearString($_POST['username']), ClearString($_POST['password']), $SavePassword);
//    if($login['success']) {
//        echo $login['message'];
//        header('Location:index.php');
//    }
//    else {
//        echo $login['message'];
//        header('Refresh:3;URL=index.php');
//    }
//
//
//}

if (isset($_POST['register'])) {
    
    $conn = new RoceanDB();

    // Έλεγχος αν συμφωνούν τα 2 passwords
    if($_POST['password']==$_POST['repeat_password']) {
        if($conn->CreateUser(ClearString($_POST['username']), ClearString($_POST['email']), ClearString($_POST['password']), 'local')) // Δημιουργεί τον χρήστη
            echo '<p>'.__('register_with_success').'</p>';
    }
    else echo '<p>'.__('not_the_same_password').'</p>';
    


}


function logout() {
    // remove all session variables
    session_unset();

// destroy the session
    session_destroy();

    // unset cookies
    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);
            setcookie($name, '', time()-1000);
            setcookie($name, '', time()-1000, '/');
        }
    }

    header('Location:index.php');
}

// Εμφάνιση επιλογών login
function showLoginWindow()
{

    $LoginWindow = new Page();



    ?>
    <main>
    <div id="LoginWindow">


        <?php


        $FormElementsArray = array(
            array('name' => 'username',
                'fieldtext' => __('form_user_name'),
                'type' => 'text',
                'onclick' => '',
                'required' => 'yes',
                'maxlength' => '15',
                'pattern' => '^[a-zA-Z0-9]+$',
                'title' => 'Give the username',
                'value' => null),
            array('name' => 'password',
                'fieldtext' => __('form_password'),
                'type' => 'password',
                'onclick' => '',
                'required' => 'yes',
                'maxlength' => '15',
                'pattern' => '^[a-zA-Z0-9]+$',  // (?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}
                'title' => 'Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters',
                'value' => null),
            array('name' => 'SavePassword',
                'fieldtext' => __('form_save_password'),
                'type' => 'checkbox',
                'onclick' => '',
                'required' => 'no',
                'maxlength' => '',
                'pattern' => '',
                'title' => '',
                'value' => 'yes'),
            array('name' => 'submit',
                'fieldtext' => '',
                'type' => 'submit',
                'onclick' => 'login(event);',
                'required' => 'no',
                'maxlength' => '',
                'pattern' => '',
                'title' => '',
                'value' => __('form_login'))
        );

        $LoginWindow->MakeForm('login();', $FormElementsArray);
        
        // TODO να το κάνω να στέλνει και όταν πατηθεί enter
        

        ?>

    </div>
    </main>


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

    $stmt->closeCursor();
    $stmt = null;


}
