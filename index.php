<?php

/**
 * File: index.php
 * Created by rocean
 * Date: 17/04/16
 * Time: 01:17
 */

require_once ('libraries/common.inc.php');
require_once ('login.php');

session_start();


$MainPage = new Page();
$conn = new RoceanDB();
$lang = new Language();


// έλεγχος αν έχει πατηθεί link για αλλαγής της γλώσσας
if (isset($_GET['ChangeLang']))
    $lang->change_lang($_GET['ChangeLang']);

// Τίτλος της σελίδας
$MainPage->tittle = PAGE_TITTLE;

$MainPage->showHeader();

$languages_text=$lang->print_languages('lang_id',' ',true,false);



// Έλεγχος αν υπάρχει cookie. Αν δεν υπάρχει ψάχνει session
if(!$conn->CheckCookiesForLoggedUser()) {
    if (isset($_SESSION["username"]))
    {

        $LoginNameText= __('user_logged_in').$crypt->DecryptText($_SESSION["username"]);
        session_regenerate_id(true);


    }
}
else $LoginNameText= __('user_logged_in').$_COOKIE["username"];

$MainPage->showMainBar($LoginNameText,$languages_text);



showLoginWindow();

$MainPage->showFooter();



?>