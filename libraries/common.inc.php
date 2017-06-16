<?php
/**
 * File: common.inc.php
 * Created by rocean
 * Date: 04/05/16
 * Time: 22:36
 */

define ('PROJECT_PATH','/arduino/');   // αν το project είναι σε κάποιον υποκατάλογο

if (!$_SERVER["DOCUMENT_ROOT"]) {  // Για τις περιπτώσεις που τρέχει από cron
    $_SERVER['DOCUMENT_ROOT'] = dirname(dirname( __FILE__ ));
}


require_once ($_SERVER["DOCUMENT_ROOT"]  .PROJECT_PATH.'libraries/Page.php');
require_once ($_SERVER["DOCUMENT_ROOT"]  .PROJECT_PATH.'libraries/Session.php');
require_once ($_SERVER["DOCUMENT_ROOT"]  .PROJECT_PATH.'libraries/RoceanDB.php');
require_once ($_SERVER["DOCUMENT_ROOT"]  .PROJECT_PATH.'libraries/Crypto.php');
require_once ($_SERVER["DOCUMENT_ROOT"]  .PROJECT_PATH.'libraries/Language.php');
require_once ($_SERVER["DOCUMENT_ROOT"]  .PROJECT_PATH.'libraries/Arduino.php');

require_once ($_SERVER["DOCUMENT_ROOT"]  .PROJECT_PATH.'libraries/config.inc.php');

define('PAGE_TITTLE','ITBusiness Smart Control Room');     // ονομασία της εφαρμογής που θα φαίνεται στον τίτλο της σελίδας

define ('LANG_PATH',$_SERVER["DOCUMENT_ROOT"]  .PROJECT_PATH.'lang/');      // το path του καταλόγου των γλωσσών. Να μην πειραχτεί
define ('LANG_PATH_HTTP',$_SERVER["HTTP_HOST"]  .PROJECT_PATH.'lang/');      // το path του καταλόγου των γλωσσών σε http. Να μην πειραχτεί


if (isset($_SERVER['HTTPS'])) define ('HTTP_TEXT', 'https://');  // αν είναι https
else define ('HTTP_TEXT', 'http://');


// Παίρνει ολόκληρο το url του project με την εσωτερική ip του server
define ('LOCAL_SERVER_IP_WITH_PORT', HTTP_TEXT.$_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'].PROJECT_PATH);

// Η διεύθυνση του script. Πρέπει να είναι ολόκληρο το url της εσωτερικής ip του server που τρέχει η εφαρμογή
// π.χ. http://192.168.1.19:9999/arduino
// αν το script τρέχει στον σερβερ της εφαρμογής, αφήνουμε αυτή την γραμμή όπως είναι, αλλιώς χρησιμοποιούμε τα παρακάτω παραδείγματα
define ('POWER_SCRIPT_ADDRESS', LOCAL_SERVER_IP_WITH_PORT.'runPowerScript.php');

// Άλλα παραδείγματα. Όταν το script τρέχει σε άλλη διεύθυνση το αλλάζουμε κάπως έτσι
//define ('POWER_SCRIPT_ADDRESS', 'http://192.168.1.150/'.'runPowerScript.php');
//define ('POWER_SCRIPT_ADDRESS', 'http://www.example/'.'runPowerScript.php');

define ('NAV_LIST_ITEMS', '6'); // Ο αριθμός των επιλογών στo Nav Menu

$adminNavItems = array(6);  // Οι αριθμοί των items που είναι μόνο για τον admin

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