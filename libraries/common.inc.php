<?php
/**
 * File: common.inc.php
 * Created by rocean
 * Date: 04/05/16
 * Time: 22:36
 */


require_once ('Page.php');
require_once ('Session.php');
require_once ('RoceanDB.php');
require_once ('Crypto.php');
require_once ('Language.php');
require_once ('Arduino.php');

define (PROJECT_PATH,'/arduino/');

define(CONNSTR, 'mysql:host=localhost;dbname=arduino_db');
define(DBUSER, 'root');
define(DBPASS, 'documents2015');

define(PAGE_TITTLE,'ArduinoDB');

define (LANG_PATH,PROJECT_PATH.'lang/');

define (NAV_LIST_ITEMS, '6'); // Ο αριθμός των επιλογών στo Nav Menu

$languages = array (
    array ('language' => 'Ελληνικά',
        'lang_id' => 'gr'),
    array ('language' => 'English',
        'lang_id' => 'en')
);


// Καθαρίζει τα data που έδωσε ο χρήστης από περίεργο κώδικα
function ClearString($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}



