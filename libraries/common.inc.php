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

define (PROJECT_PATH,'/petsdb/');

define(CONNSTR, 'mysql:host=localhost;dbname=pets');
define(DBUSER, 'root');
define(DBPASS, 'documents2015');

define(PAGE_TITTLE,'PetsDB');

define (LANG_PATH,PROJECT_PATH.'lang/');

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



