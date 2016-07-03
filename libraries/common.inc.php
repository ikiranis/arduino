<?php
/**
 * File: common.inc.php
 * Created by rocean
 * Date: 04/05/16
 * Time: 22:36
 */

define ('PROJECT_PATH','/arduino/');   // αν το project είναι σε κάποιον υποκατάλογο

if (!$_SERVER["DOCUMENT_ROOT"]) {  // Για τις περιπτώσεις που τρέχει από cron
    $_SERVER['DOCUMENT_ROOT'] = dirname(dirname(dirname( __FILE__ )));
}


require_once ($_SERVER["DOCUMENT_ROOT"]  .PROJECT_PATH.'libraries/Page.php');
require_once ($_SERVER["DOCUMENT_ROOT"]  .PROJECT_PATH.'libraries/Session.php');
require_once ($_SERVER["DOCUMENT_ROOT"]  .PROJECT_PATH.'libraries/RoceanDB.php');
require_once ($_SERVER["DOCUMENT_ROOT"]  .PROJECT_PATH.'libraries/Crypto.php');
require_once ($_SERVER["DOCUMENT_ROOT"]  .PROJECT_PATH.'libraries/Language.php');
require_once ($_SERVER["DOCUMENT_ROOT"]  .PROJECT_PATH.'libraries/Arduino.php');



define('CONNSTR', 'mysql:host=localhost;dbname=arduino_db');
define('DBUSER', 'root');
define('DBPASS', 'documents2015');

define('PAGE_TITTLE','ITBusiness Smart Control Room');     // ονομασία της εφαρμογής που θα φαίνεται στον τίτλο της σελίδας


define ('LANG_PATH',$_SERVER["DOCUMENT_ROOT"]  .PROJECT_PATH.'lang/');      // το path του καταλόγου των γλωσσών. Να μην πειραχτεί
define ('LANG_PATH_HTTP',$_SERVER["HTTP_HOST"]  .PROJECT_PATH.'lang/');      // το path του καταλόγου των γλωσσών σε http. Να μην πειραχτεί


define ('NAV_LIST_ITEMS', '6'); // Ο αριθμός των επιλογών στo Nav Menu

$languages = array (    // Οι γλώσσες που υποστηρίζονται
    array ('language' => 'Ελληνικά',
        'lang_id' => 'gr'),
    array ('language' => 'English',
        'lang_id' => 'en')
);

$UserGroups = array (     // Τα user groups που υπάρχουν
    array ('id' => '1',
        'group_name' => 'admin'),
    array ('id' => '2',
        'group_name' => 'user')
);



// Public functions

// Καθαρίζει τα data που έδωσε ο χρήστης από περίεργο κώδικα
function ClearString($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}




// Μεταβλητές για το Arduino project

$sensorsArray=RoceanDB::getTableArray('sensors','id, db_field', null, null, null); // Αρχικοποίηση του $SensorsArray

define('INTERVAL_VALUE', RoceanDB::getOption('interval_value')); // Κάθε πόσα δευτερόλεπτα θα κάνει ανανέωση εισερχόμενων
define('DATE_LIST_ITEMS', RoceanDB::getOption('date_list_items'));   // Πόσες επιλογές χρονικής περιόδου θα έχει στα στατιστικά
define('CPU_FIELD', RoceanDB::getOption('cpu_field'));






