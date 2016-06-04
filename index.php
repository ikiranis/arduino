<?php

/**
 * File: index.php
 * Created by rocean
 * Date: 17/04/16
 * Time: 01:17
 */

require_once ('libraries/common.inc.php');
require_once ('login.php');
require_once ('MainPage.php');

session_start();


$MainPage = new Page();
$conn = new RoceanDB();
$lang = new Language();


// έλεγχος αν έχει πατηθεί link για αλλαγής της γλώσσας
if (isset($_GET['ChangeLang'])) {
    $targetPage='Location:index.php';

    $lang->change_lang($_GET['ChangeLang']);

    header($targetPage);
}

if (isset($_GET['logout']))
    logout();

// Τίτλος της σελίδας
$MainPage->tittle = PAGE_TITTLE;

$scripts=array ('src=libraries/jquery.min.js', 'src=libraries/scripts.js', 
    'src=libraries/details.js', 'src=https://www.gstatic.com/charts/loader.js');

$MainPage->setScript($scripts);

$MainPage->showHeader();

//$languages_text=$lang->print_languages('lang_id',' ',true,false);

$logged_in=false;

// Έλεγχος αν υπάρχει cookie. Αν δεν υπάρχει ψάχνει session
if(!$conn->CheckCookiesForLoggedUser()) {
    if (isset($_SESSION["username"]))
    {

        $LoginNameText= '<img id=account_image src=img/account.png> <span id=account_name>'.$conn->getSession('username').'</span>';
//        session_regenerate_id(true);

        $logged_in=true;

    }
}
else {
    $LoginNameText= '<img id=account_image src=img/account.png> <span id=account_name>'.$_COOKIE["username"].'</span>';
    $logged_in=true;
}


if($logged_in)
    $LoginNameText.=' <span id=logout><a href=?logout=true title='.__('logout').'><img src=img/exit.png></a></span>';

$timediv='<div id=SystemTime><img src=img/time.png><span id="timetext"></span></div>';

$MainPage->showMainBar($timediv, $LoginNameText);



if(!$logged_in) showLoginWindow();

if($logged_in) DisplayMainPage();



$MainPage->showFooter();





?>

