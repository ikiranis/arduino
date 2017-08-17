<?php

/**
 * File: Arduino.php
 * Created by rocean
 * Date: 20/05/16
 * Time: 22:02
 */


class Arduino
{

    // Επαναφέρει τους διακόπτες στην αρχική τους κατάσταση, πριν να κλείσει το σύστημα
    static function putSwitchesToPreviousPosition () {
        $conn = new RoceanDB();


        $powers=$conn->getTableArray('power', 'id, status', null, null, null);  // Παίρνει τα δεδομένα του πίνακα power σε array

        foreach ($powers as $power) {


            if ($power['status']=='ON')
                self::runPowerScript($power['id'], 'ON', $power['mac_address']);
                
        }
        
    }

    static function get_content($URL){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $URL);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }


    // Τρέχει το κατάλληλο script για να ανοίξει ή να κλεισει ο διακόπτης
    // $id: το id του διακόπτη στην βάση
    // $newStatus: Το νέο status που παίρνει ο διακόπτης ON ή OFF
    // $macAddress: H mac address του διακόπτη
    static function runPowerScript ($id, $newStatus, $macAddress) {

        if($newStatus=='ON') {
            $newStatus='OFF';
        } else {
            $newStatus='ON';
        }

        // Παίρνουμε σε JSON το αποτέλεσμα του script που επιστρέφει true ή false
        $html = 'http://'.$macAddress.'/'.$newStatus;
        $response = file_get_contents($html);

        if($response) {
            return true;
        } else {
            return false;
        }
        
//        trigger_error('Opening '.$id.' '.$newStatus.' '.urlencode($macAddress));


    }
    
    


    // Επιστρέφει τις τελευταίες θερμοκρασίες σε array
    static function getAvgLastTemperatures() {
        $conn = new RoceanDB();
        $conn->CreateConnection();

        $sensors=$conn->getTableArray('sensors','db_field', null, null, null);   // Παίρνει τα δεδομένα του πίνακα sensors σε array
        $counter=1;
        $sql_string='';

        foreach ($sensors as $sensor) {   // δημιουργεί το $sql_string που θα προστεθεί στο $sql
            $sql_string.='round(avg(LastItems.'.$sensor['db_field'].')) as avg'.$counter.', ';
            $counter++;
        }

        $sql_string=Page::cutLastString($sql_string,', ');    // κόβει το τελευταίο ', '

        $sql = 'SELECT '.$sql_string.' FROM (SELECT * FROM data ORDER BY time desc LIMIT 0,12) LastItems';

        $stmt = RoceanDB::$conn->prepare($sql);

        $stmt->execute();

        global $sensorsArray;
        $jsonArray=array();

        if($item=$stmt->fetch(PDO::FETCH_ASSOC))
        {

            $counter=1;
            $i=0;
            foreach ($sensorsArray as $sensor) {
                $jsonArray=$jsonArray+array('temp'.$counter=>$item['avg'.$counter]);
                $counter++;
                $i++;
            }

            return $jsonArray;


        }

        $stmt->closeCursor();
        $stmt = null;
    }

    // Επιστρέφει τις τελευταίες θερμοκρασίες σε array
    static function getLastCPUTemp($cpufield) {
        $conn = new RoceanDB();
        $conn->CreateConnection();

        $sql = 'SELECT '.$cpufield.' FROM data ORDER BY time DESC LIMIT 0,1';
        $stmt = RoceanDB::$conn->prepare($sql);

        $stmt->execute();


        if($item=$stmt->fetch(PDO::FETCH_ASSOC))
        {
            $result=$item[$cpufield];

        }
        else $result=false;
        
        return $result;

        $stmt->closeCursor();
        $stmt = null;
    }


    // Επιστρέφει τις τελευταίες θερμοκρασίες σε array
    static function getLastTemperatures() {
        $conn = new RoceanDB();
        $conn->CreateConnection();

        $sql = 'SELECT * FROM data ORDER BY time DESC LIMIT 0,1';
        $stmt = RoceanDB::$conn->prepare($sql);

        $stmt->execute();

        global $sensorsArray;
        $jsonArray=array();


        if($item=$stmt->fetch(PDO::FETCH_ASSOC))
        {
            $counter=1;
            foreach ($sensorsArray as $sensor) {
                $jsonArray=$jsonArray+array('probe'.$counter=>$item[$sensor['db_field']]);
                $counter++;
            }
            $jsonArray=$jsonArray+array("time"=>$item['time']);

           return $jsonArray;


        }

        $stmt->closeCursor();
        $stmt = null;
    }

    // Επιστρέφει μόνο τις θερμοκρασίες με το όνομα του sensor σε array
    static function getOnlyLastTemperatures() {
        $conn = new RoceanDB();
        $conn->CreateConnection();

        $sql = 'SELECT * FROM data ORDER BY time DESC LIMIT 0,1';
        $stmt = RoceanDB::$conn->prepare($sql);

        $stmt->execute();

        global $sensorsArray;
        $jsonArray=array();


        if($item=$stmt->fetch(PDO::FETCH_ASSOC))
        {
            $counter=1;
            foreach ($sensorsArray as $sensor) {
                $jsonArray[]=array('sensor'=>$sensor['db_field'], 'temp'=>$item[$sensor['db_field']]);
                $counter++;
            }

            return $jsonArray;


        }

        $stmt->closeCursor();
        $stmt = null;
    }

    // Βάζει την τρέχουσα ώρα στο alert με $id
    static function setTimeToAlert ($id) {
        $conn = new RoceanDB();
        $conn->CreateConnection();


        $sql = 'UPDATE alerts SET alert_time=? WHERE id=?';
        $stmt = RoceanDB::$conn->prepare($sql);

        if($stmt->execute(array(date('Y-m-d H:i:s'), $id)))
            return true;
        else return false;

        $stmt->closeCursor();
        $stmt = null;

    }

    // Επιστρέφει το Sensor id του $db_field
    static function getSensorID($db_field) {
        $conn = new RoceanDB();
        $conn->CreateConnection();

        $sql='SELECT id FROM sensors WHERE db_field=?';

        $stmt = RoceanDB::$conn->prepare($sql);

        $stmt->execute(array($db_field));

        if($item=$stmt->fetch(PDO::FETCH_ASSOC))
        {
            $result=$item['id'];
        }
        else $result=false;

        return $result;

        $stmt->closeCursor();
        $stmt = null;
    }

    // Εμφάνιση των εγγραφών των sensors σε μορφή form fields για editing
    static function getSensorsInFormFields () {
        $conn = new RoceanDB();


        $sensors=$conn->getTableArray('sensors', null, null, null, null);  // Παίρνει τα δεδομένα του πίνακα sensors σε array

        if(empty($sensors)) {  // Αν δεν επιστρέψει κανένα αποτέλεσμα, σετάρουμε εμείς μια πρώτη γραμμή στο array
            $sensors[]=array('id'=>'0', 'room'=>'', 'sensor_name'=>'', 'db_field'=>'');
        }
        
        $counter=1;


        ?>
            <div class="ListTable">
            
            

        <?php


                foreach($sensors as $sensor)
                {
                ?>

                    <div class="SensorsRow" id="SensorID<?php echo $sensor['id']; ?>">
                        <form class="table_form sensors_form" id="sensors_formID<?php echo $sensor['id']; ?>">
                        <span class="ListColumn"><input class="input_field"
                                                        placeholder="<?php echo __('sensors_room'); ?>"
                                                        title="<?php echo __('valid_room'); ?>"
                                                        pattern='^[a-zA-ZΆ-Ϋά-ώ][a-zA-ZΆ-Ϋά-ώ0-9-_\.]{2,15}$'
                                                        maxlength="15" required type="text" id="room" name="room"
                                                        value="<?php echo $sensor['room']; ?>"></span>
                        <span class="ListColumn"><input class="input_field"
                                                        placeholder="<?php echo __('sensors_sensor'); ?>"
                                                        title="<?php echo __('valid_sensor'); ?>"
                                                        pattern='^[a-zA-ZΆ-Ϋά-ώ ][a-zA-ZΆ-Ϋά-ώ0-9- _\.]{2,20}$'
                                                        maxlength="20"  type="text"  id="sensor_name" name="sensor_name" value="<?php echo $sensor['sensor_name']; ?>"></span>
                        <span class="ListColumn"><input class="input_field"
                                                        placeholder="<?php echo __('sensors_dbfield'); ?>"
                                                        title="<?php echo __('valid_dbfield'); ?>"
                                                        pattern='^[a-zA-Z][a-zA-Z0-9-_\.]{2,20}$'
                                                        maxlength="20" required type="text"  id=="db_field" name="db_field" value="<?php echo $sensor['db_field']; ?>"></span>

                        <input type="button" class="update_button button_img" name="update_sensor" title="<?php echo __('update_row'); ?>" onclick="updateSensor(<?php echo $sensor['id']; ?>);">

                        <input type="button" class="delete_button button_img <?php if($counter==1) echo 'dontDelete'; ?>" name="delete_sensor" title="<?php echo __('delete_row'); ?>" onclick="deleteSensor(<?php echo $sensor['id']; ?>);">

                        <input type="button" class="message" id="messageID<?php echo $sensor['id']; ?>">
                        </form>
                    </div>

                    <?php
                    $counter++;
                }
                ?>

            </div>
            <input type="button" class="insert_row" name="insert_sensor" onclick="insertSensor();" value="<?php echo __('insert_row'); ?>">
            
        <?php



    }

    // Εμφάνιση των εγγραφών των power σε μορφή form fields για editing
    static function getPowerInFormFields () {
        $conn = new RoceanDB();


        $powers=$conn->getTableArray('power', null, null, null, null);  // Παίρνει τα δεδομένα του πίνακα power σε array

        if(empty($powers)) {  // Αν δεν επιστρέψει κανένα αποτέλεσμα, σετάρουμε εμείς μια πρώτη γραμμή στο array
            $powers[]=array('id'=>'0', 'room'=>'', 'power_name'=>'');
        }
        
        $counter=1;


        ?>
            <div class="ListTable">

                


        <?php


                foreach($powers as $power)
                {
                    ?>
                    <div class="PowersRow" id="PowerID<?php echo $power['id']; ?>">
                        <form class="table_form powers_form" id="powers_formID<?php echo $power['id']; ?>">
                        <span class="ListColumn"><input class="input_field"
                                                        placeholder="<?php echo __('power_room'); ?>"
                                                        title="<?php echo __('valid_room'); ?>"
                                                        pattern='^[a-zA-ZΆ-Ϋά-ώ][a-zA-ZΆ-Ϋά-ώ0-9-_\.]{2,15}$'
                                                        maxlength="15" required type="text" name="room" value="<?php echo $power['room']; ?>"></span>
                        <span class="ListColumn"><input class="input_field"
                                                        placeholder="<?php echo __('power_switch'); ?>"
                                                        title="<?php echo __('valid_power_name'); ?>"
                                                        pattern='^[a-zA-ZΆ-Ϋά-ώ ][a-zA-ZΆ-Ϋά-ώ0-9- _\.]{2,20}$'
                                                        maxlength="20" required type="text" name="power_name" value="<?php echo $power['power_name']; ?>"></span>
                        <span class="ListColumn"><input class="input_field"
                                                        placeholder="<?php echo __('power_mac_address'); ?>"
                                                        title="<?php echo __('valid_power_mac_address'); ?>"
                                                        minlength="5" maxlength="30" required type="text" name="power_mac_address" value="<?php echo $power['mac_address']; ?>"></span>
                        
                        <input type="button" class="update_button button_img" name="update_power" title="<?php echo __('update_row'); ?>" onclick="updatePower(<?php echo $power['id']; ?>);"">
 
                        <input type="button" class="delete_button button_img <?php if($counter==1) echo 'dontDelete'; ?>" name="delete_power" title="<?php echo __('delete_row'); ?>" onclick="deletePower(<?php echo $power['id']; ?>);"">
 
                        <input type="button" class="message" id="messagePowerID<?php echo $power['id']; ?>">
                        </form>

                    </div>
                    <?php
                    $counter++;
                }
                ?>

            </div>
            <input type="button" class="insert_row" name="insert_power" onclick="insertPower();" value="<?php echo __('insert_row'); ?>">

        <?php


    }


    // Εμφάνιση των εγγραφών των χρηστών σε μορφή form fields για editing
    static function getUsersInFormFields () {
        $conn = new RoceanDB();
        $conn->CreateConnection();

        $UserGroupID=$conn->getUserGroup($conn->getSession('username'));  // Παίρνει το user group στο οποίο ανήκει ο χρήστης
        $userID=$conn->getUserID($conn->getSession('username'));      // Επιστρέφει το id του user με username στο session

        global $UserGroups;

        if($UserGroupID==1)
            $sql = 'SELECT * FROM user JOIN user_details on user.user_id=user_details.user_id';
        else $sql = 'SELECT * FROM user JOIN user_details on user.user_id=user_details.user_id WHERE user.user_id=?';

        $stmt = RoceanDB::$conn->prepare($sql);
        
        $counter=1;

        if($UserGroupID==1)
            $stmt->execute();
        else $stmt->execute(array($userID));

        ?>
        <div class="ListTable UsersList">




            <?php


            while($item=$stmt->fetch(PDO::FETCH_ASSOC))
            {
                ?>
                <div class="UsersRow" id="UserID<?php echo $item['user_id']; ?>">
                    <form class="table_form users_form" id="users_formID<?php echo $item['user_id']; ?>">
                    <span class="ListColumn">
                        <input class="input_field"
                               placeholder="<?php echo __('users_username'); ?>"
                               title="<?php echo __('valid_username'); ?>"
                               pattern='^[a-zA-Z][a-zA-Z0-9-_\.]{4,15}$'
                               maxlength="15" required type="text" name="username" value="<?php echo $item['username']; ?>">
                    </span>
                    <span class="ListColumn">
                        <input class="input_field"
                               placeholder="<?php echo __('users_email'); ?>"
                               title="<?php echo __('valid_email'); ?>"
                               maxlength="50" required type="email" name="email" value="<?php echo $item['email']; ?>">
                    </span>
                    <span class="ListColumn">
                        <input class="input_field"
                               placeholder="<?php echo __('users_password'); ?>"
                               title="<?php echo __('valid_register_password'); ?>"
                               pattern='(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}'
                               maxlength="15"  type="password" id="password<?php echo $item['user_id']; ?>" name="password" value="">
                    </span>
                    <span class="ListColumn">
                        <input class="input_field"
                               placeholder="<?php echo __('users_repeat_password'); ?>"
                               title="<?php echo __('valid_register_repeat_password'); ?>"
                               pattern='(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}'
                               maxlength="15"  type="password" id="<?php echo $item['user_id']; ?>" name="repeat_password" value="">
                    </span>





                    <span class="ListColumn">
                        <select class="input_field" name="usergroup" <?php if($UserGroupID!=1) echo ' disabled=disabled'; ?> >
                            <?php
                            foreach ($UserGroups as $UserGroup) {
                                ?>
                                <option value="<?php echo $UserGroup['id']; ?>"
                                    <?php if($UserGroup['id']==$item['user_group']) echo 'selected=selected'; ?>>
                                    <?php echo $UserGroup['group_name']; ?>
                                </option>

                                <?php
                            }
                            ?>
                        </select>
                    </span>
                    <span class="ListColumn">
                        <input class="input_field"
                               placeholder="<?php echo __('users_firstname'); ?>"
                               title="<?php echo __('valid_fname'); ?>"
                               pattern='^[a-zA-ZΆ-Ϋά-ώ][a-zA-ZΆ-Ϋά-ώ0-9-_\.]{2,15}$'
                               maxlength="15"  type="text" name="fname" value="<?php echo $item['fname']; ?>">
                    </span>
                    <span class="ListColumn">
                        <input class="input_field"
                               placeholder="<?php echo __('users_lastname'); ?>"
                               title="<?php echo __('valid_lname'); ?>"
                               pattern='^[a-zA-ZΆ-Ϋά-ώ][a-zA-ZΆ-Ϋά-ώ0-9-_\.]{2,25}$'
                               maxlength="25"  type="text" name="lname" value="<?php echo $item['lname']; ?>">
                    </span>

                    <input type="button" class="update_button button_img" name="update_user" title="<?php echo __('update_row'); ?>" onclick="updateUser(<?php echo $item['user_id']; ?>);"">

                    <input type="button" class="delete_button button_img <?php if($counter==1) echo 'dontDelete'; ?>" name="delete_user" title="<?php echo __('delete_row'); ?>" onclick="deleteUser(<?php echo $item['user_id']; ?>);"">

                    <input type="button" class="message" id="messageUserID<?php echo $item['user_id']; ?>">
                    </form>
                </div>
                <?php
                $counter++;
            }
            ?>

        </div>

        <?php
        if($UserGroupID==1) {  // Αν είναι admin ο user εμφάνισε κουμπί για προσθήκη νέου user
            ?>

            <input type="button" class="insert_row" name="insert_user" onclick="insertUser();" value="<?php echo __('insert_row'); ?>">
            <?php
        }
        ?>

        <?php
        $stmt->closeCursor();
        $stmt = null;

    }


    // Εμφάνιση των εγγραφών των alerts σε μορφή form fields για editing
    static function getAlertsInFormFields () {
        $conn = new RoceanDB();

        $userID=$conn->getUserID($conn->getSession('username'));      // Επιστρέφει το id του user με username στο session
        $alerts=$conn->getTableArray('alerts', null, 'user_id=?', array($userID), null);  // Παίρνει τα δεδομένα του πίνακα alerts σε array
        $sensors=$conn->getTableArray('sensors', null, null, null, null);   // Παίρνει τα δεδομένα του πίνακα sensors σε array

        if(empty($alerts)) {  // Αν δεν επιστρέψει κανένα αποτέλεσμα, σετάρουμε εμείς μια πρώτη γραμμή στο array
            $alerts[]=array('id'=>'0', 'email'=>'', 'time_limit'=>'', 'temp_limit'=>'', 'sensors_id'=>'', 'user_id'=>'');
        }
        
        $counter=1;

        ?>

        <div class="ListTable">

        <?php

        foreach ($alerts as $alert)
        {
            ?>
                <div class="AlertsRow" id="AlertID<?php echo $alert['id']; ?>">
                    <form class="table_form alerts_form" id="alerts_formID<?php echo $alert['id']; ?>">
                    <span class="ListColumn"><input class="input_field"
                                                    placeholder="<?php echo __('alerts_email'); ?>"
                                                    title="<?php echo __('valid_email'); ?>"
                                                    maxlength="50" required type="email" name="email" value="<?php echo $alert['email']; ?>"></span>
                    <span class="ListColumn"><input class="input_field"
                                                    placeholder="<?php echo __('alerts_timelimit'); ?>"
                                                    title="<?php echo __('valid_time_limit'); ?>"
                                                    pattern='\d+'
                                                    maxlength="2" required type="text" name="time_limit" value="<?php echo $alert['time_limit']; ?>"></span>
                    <span class="ListColumn"><input class="input_field"
                                                    placeholder="<?php echo __('alerts_templimit'); ?>"
                                                    title="<?php echo __('valid_temp_limit'); ?>"
                                                    pattern='\d+'
                                                    maxlength="2" required type="text" name="temp_limit" value="<?php echo $alert['temp_limit']; ?>"></span>
                    <span class="ListColumn">
                        <select class="input_field" name="sensors_list">
                            <?php
                                foreach ($sensors as $sensor) {
                                    ?>
                                        <option value="<?php echo $sensor['id']; ?>"
                                                <?php if($sensor['id']==$alert['sensors_id']) echo 'selected=selected'; ?>>
                                            <?php echo $sensor['room'].' '.$sensor['sensor_name']; ?>
                                        </option>

                                    <?php
                                }
                            ?>
                        </select>
                    </span>
                    <input type="hidden" name="user_id" value="<?php echo $userID; ?>">

                    <input type="button" class="update_button button_img" name="update_alert" title="<?php echo __('update_row'); ?>" onclick="updateAlert(<?php echo $alert['id']; ?>);"">

                    <input type="button" class="delete_button button_img <?php if($counter==1) echo 'dontDelete'; ?>" name="delete_alert" title="<?php echo __('delete_row'); ?>" onclick="deleteAlert(<?php echo $alert['id']; ?>);"">

                    <input type="button" class="message" id="messageAlertID<?php echo $alert['id']; ?>">
                    </form>
                </div>
                <?php
                $counter++;
            }
            ?>

        
        </div>
        <input type="button" class="insert_row" name="insert_alert" onclick="insertAlert();" value="<?php echo __('insert_row'); ?>">

        <?php


    }

    // Εμφάνιση των εγγραφών των options σε μορφή form fields για editing
    static function getOptionsInFormFields () {
        $conn = new RoceanDB();

        $options=$conn->getTableArray('options', null, 'setting=?', array(1), null);  // Παίρνει τα δεδομένα του πίνακα alerts σε array


        ?>

        <div class="ListTable">

            <?php

            foreach ($options as $option)
            {
                ?>
                <div class="OptionsRow" id="OptionID<?php echo $option['option_id']; ?>">
                    <form class="table_form options_form" id="options_formID<?php echo $option['option_id']; ?>">
                    <span class="ListColumn"><input class="input_field" disabled
                                                    placeholder="<?php echo __('options_option'); ?>"
                                                    type="text" name="option_name" value="<?php echo $option['option_name']; ?>"></span>
                    <span class="ListColumn"><input class="input_field"
                                                    placeholder="<?php echo __('options_value'); ?>"
                                                    title="<?php echo __('valid_option'); ?>"

                                                    maxlength="255" required type="<?php if($option['encrypt']==0) echo 'text'; else echo 'password'; ?>" name="option_value" value="<?php if($option['encrypt']==0) echo $option['option_value']; ?>"></span>

                        <input type="button" class="update_button button_img" name="update_option" title="<?php echo __('update_row'); ?>" onclick="updateOption(<?php echo $option['option_id']; ?>);"">

                        <input type="button" class="message" id="messageOptionID<?php echo $option['option_id']; ?>">
                    </form>
                </div>
                <?php
            }
            ?>


        </div>

        <?php


    }

    // Θέτει το $newStatus στον διακόπτη $id
    static function setPowerStatus ($id, $newStatus) {
        $conn = new RoceanDB();
        $conn->CreateConnection();
 
        $sql = 'UPDATE power SET status=? WHERE id=?';
        $stmt = RoceanDB::$conn->prepare($sql);

        if($stmt->execute(array($newStatus, $id)))
            $result=true;
        else $result=false;

        $stmt->closeCursor();
        $stmt = null;
        
        return $result;
    }

    static function getPowerStatus($id) {

        $conn = new RoceanDB();
        $conn->CreateConnection();

        $sql = 'SELECT status FROM power WHERE id=?';
        $stmt = RoceanDB::$conn->prepare($sql);

        $stmt->execute(array($id));


        if($item=$stmt->fetch(PDO::FETCH_ASSOC))
        {

            $result= $item['status'];

        }

        $stmt->closeCursor();
        $stmt = null;
        
        return $result;

    }

    // Επιστρέφει το όνομα του διακόπτη $id
    static function getPowerName($id) {

        $conn = new RoceanDB();
        $conn->CreateConnection();

        $sql = 'SELECT room, power_name FROM power WHERE id=?';
        $stmt = RoceanDB::$conn->prepare($sql);

        $stmt->execute(array($id));


        if($item=$stmt->fetch(PDO::FETCH_ASSOC))
        {

            $result= $item['room'].':'.$item['power_name'];

        }

        $stmt->closeCursor();
        $stmt = null;
        
        return $result;

    }

    // Επιστρέφει την mac address του $id
    static function getPowerMac($id) {

        $conn = new RoceanDB();
        $conn->CreateConnection();

        $sql = 'SELECT mac_address FROM power WHERE id=?';
        $stmt = RoceanDB::$conn->prepare($sql);

        $stmt->execute(array($id));


        if($item=$stmt->fetch(PDO::FETCH_ASSOC))
        {

            return $item['mac_address'];

        }

        $stmt->closeCursor();
        $stmt = null;

    }


    static function showDashboard () {
        $conn = new RoceanDB();
        ?>
        <h2><?php echo __('nav_item_1'); ?></h2>
        
        <p><?php echo __('system_temperature'); ?><span id="cputemp">0</span><?php echo '&deg; C'?></p>
        <p><?php echo __('sensors_status');?><span id="dbstatus"></span></p>

        <?php
            $userID=$conn->getUserID($conn->getSession('username'));      // Επιστρέφει το id του user με username στο session
            $alerts=$conn->getTableArray('alerts', null, 'user_id=?', array($userID), null);  // Παίρνει τα δεδομένα του πίνακα alerts σε array
            $sensors=$conn->getTableArray('sensors', null, null, null, null);   // Παίρνει τα δεδομένα του πίνακα sensors σε array

            if($alerts) { // αν το $alerts δεν είναι κενό τότε τύπωσε τα

                ?>

                <h3><?php echo __('settings_alerts'); ?></h3>

                <div id="dasboard_alerts" class="ListTable">

                    <div class="AlertsRow ListTittleRow">
                        <div class="ListColumn"><span><?php echo __('alerts_email'); ?></span></div>
                        <div class="ListColumn"><span><?php echo __('alerts_templimit'); ?></span></div>
                        <div class="ListColumn"><span><?php echo __('alerts_sensor'); ?></span></div>
                        <div class="ListColumn"><span><?php echo __('alerts_alert_time'); ?></span></div>
                    </div>

                    <?php


                    foreach ($alerts as $alert) {
                        ?>
                        <div class="AlertsRow">
                            <div class="ListColumn"><span><?php echo $alert['email']; ?></span></div>
                            <div class="ListColumn"><span><?php echo $alert['temp_limit']; ?></span></div>
                            <?php
                            $key = array_search($alert['sensors_id'], array_column($sensors, 'id'));
                            ?>
                            <div
                                class="ListColumn"><span><?php echo $sensors[$key]['room'] . ' : ' . $sensors[$key]['sensor_name']; ?></span></div>
                            <div class="ListColumn"><span><?php echo $alert['alert_time']; ?></span></div>
                        </div>

                        <?php
                    }

                    ?>
                </div>
                <?php
            }
                ?>


        <script type="text/javascript">
            checkCPUtemp('#cputemp');
            checkIfMysqlIsAlive('#dbstatus');
            setInterval(function(){
                checkIfMysqlIsAlive('#dbstatus');
                checkCPUtemp('#cputemp');

            }, IntervalValue*1000);

        </script>
        
        
        <?php
    }

    static function showTemperatures () {
        ?>
        <h2><?php echo __('nav_item_2'); ?></h2>

        <?php
            $conn = new RoceanDB();
            $conn->CreateConnection();
    
            $sql = 'SELECT * FROM sensors';
            $stmt = RoceanDB::$conn->prepare($sql);
    
            $stmt->execute();
            $counter=1;

            $sensorsIDArray=array();
    
            while($sensor=$stmt->fetch(PDO::FETCH_ASSOC))
            {


                $sensorsIDArray[]= $counter;


                echo '<div id="TempBlock'.$counter.'" class="temperature_block equal">';
                    echo '<span class=temperature_text><span id=temp'.$counter.'>0</span>&deg; C</span>';
                    echo '<span class=room_text>'.$sensor['room'].'</span>';
                    echo '<span class=sensor_name_text>'.$sensor['sensor_name'].'</span>';
                    echo '<span class=time_text id=time'.$counter.'>'.date('Y-m-d H:i:s',time()).'</span>';
                echo '</div>';

                $counter++;

            }
            ?>
        
            <!--        Στέλνουμε τα array στην javascript-->
            <script type="text/javascript">
                
                var SensorsIDArray= <?php echo json_encode($sensorsIDArray); ?>;

                getTemperature ();

                CheckTemperatures(); // onload τρέχει τον συνεχώμενο έλεγχο των θερμοκρασιών
            
            </script>



    <?php

        $stmt->closeCursor();
        $stmt = null;
    }

    static function showPower () {
        ?>
        <h2><?php echo __('nav_item_3'); ?></h2>

        <?php
            $powerDivsArray=array();
            $powerIDArray=array();

            $conn = new RoceanDB();
            $conn->CreateConnection();

            $sql = 'SELECT * FROM power';
            $stmt = RoceanDB::$conn->prepare($sql);

            $stmt->execute();

            while($power=$stmt->fetch(PDO::FETCH_ASSOC))
            {


                if($power['status']=="ON") {
                    $onoff=' powerON';
                }
                else {
                    $onoff=' powerOFF';
                }

                $powerDivsArray[]='powerID'.$power['id'];
                $powerIDArray[]= $power['id'];

                echo '<div id=powerID'.$power['id'].' class="power_block'.$onoff.'"">';
                    echo '<span class=room_text>'.$power['room'].'</span>';
                    echo '<span class=sensor_name_text>'.$power['power_name'].'</span>';
                    echo '<span class=onoff_text id=powerIDtext'.$power['id'].'>'.$power['status'].'</span>';
                echo '</div>';

            } ?>

<!--        Στέλνουμε τα array στην javascript-->
            <script type="text/javascript">   

                var PowerDivsArray= <?php echo json_encode($powerDivsArray); ?>;
                var PowerIDArray= <?php echo json_encode($powerIDArray); ?>;

                getPowerDivs();

            </script>



        <?php

        $stmt->closeCursor();
        $stmt = null;
    }

    static function showStatistics () {
        ?>
        <h2><?php echo __('nav_item_4'); ?></h2>

        <?php
            $conn = new RoceanDB();

            $sensors=$conn->getTableArray('sensors', null, null, null, null);   // Παίρνει τα δεδομένα του πίνακα sensors σε array

        ?>

        <div id="SelectGraph">
            <select name="sensors_list">
                <?php
                foreach ($sensors as $sensor) {
                    ?>
                    <option value="<?php echo $sensor['db_field']; ?>">
                        <?php echo $sensor['room'].' '.$sensor['sensor_name']; ?>
                    </option>

                    <?php
                }
                ?>
            </select>
            
            <select name="date_list">
                <?php
                for($counter=1; $counter<=DATE_LIST_ITEMS; $counter++) {
                    ?>
                    <option value="<?php echo $counter; ?>">
                        <?php echo __('stats_date_item'.$counter); ?>
                    </option>

                    <?php
                }
                ?>
            </select>

            <button name="show_statistics" onclick="RunStatistics();"><?php echo __('show_statistcs'); ?></button>
        </div>


        <div id="chart_div"></div>
        <div id="progress"></div>
        

        <?php

    }

    static function showConfiguration () {
        $conn = new RoceanDB();
        $UserGroup=$conn->getUserGroup($conn->getSession('username'));  // Παίρνει το user group στο οποίο ανήκει ο χρήστης

        ?>
        <h2><?php echo __('nav_item_5'); ?></h2>


        <details>
            <summary><?php echo __('settings_users'); ?></summary>
            <?php Arduino::getUsersInFormFields() ?>
        </details>


        <details>
            <summary><?php echo __('settings_alerts'); ?></summary>
                <?php Arduino::getAlertsInFormFields () ?>
        </details>


        <?php

        if($UserGroup==1) {  // Αν ο χρήστης είναι admin
            ?>
            <details>
                <summary><?php echo __('settings_options'); ?></summary>
                <?php Arduino::getOptionsInFormFields() ?>
            </details>

            <details>
                <summary><?php echo __('settings_sensors'); ?></summary>
                <?php Arduino::getSensorsInFormFields() ?>
            </details>

            <details>
                <summary><?php echo __('settings_power'); ?></summary>
                <?php Arduino::getPowerInFormFields() ?>
            </details>

            <p><?php echo __('options_clear_text_1'); ?><input type="number" id="clearDays" name="clearDays" placeholder="Μέρες"><?php echo __('options_clear_text_2'); ?>
            <input type="button" id="clearData" name="clearData" onclick="clearData('<?php echo __('options_clear_sure_question'); ?>');" value="<?php echo __('options_clear_data'); ?>">
            <div id="clearResponse"></div></p>
            <div id="progress"></div>

            <?php

            echo '<p>'.__('options_crontab').'<br>'.Page::getCrontab().'</p>';
        }
        ?>

        

        <div id="error_container">
            <div id="alert_error"></div>
        </div>

        <script type="text/javascript">

            var error1='<?php echo __('user_error1'); ?>';
            var error2='<?php echo __('user_error2'); ?>';

        </script>

        <?php

    }

    static function showLogs ()
    {
        ?>
        <h2><?php echo __('nav_item_6'); ?></h2>
        <?php

        $conn = new RoceanDB();
        $conn->CreateConnection();

        $sql = 'SELECT * FROM logs ORDER BY log_date DESC LIMIT 0,100';

        $stmt = RoceanDB::$conn->prepare($sql);

        $stmt->execute();

        echo '<div id=logs>';

        echo '<div class=row>';
        echo '<span class="col logs_id basic">id</span>';
        echo '<span class="col logs_message basic">message</span>';
        echo '<span class="col logs_ip basic">ip</span>';
        echo '<span class="col logs_user basic">user</span>';
        echo '<span class="col logs_date basic">date</span>';
        echo '<span class="col logs_browser basic">browser</span>';
        echo '</div>';


        // Αν ο χρήστης username βρεθεί. Αν υπάρχει δηλαδή στην βάση μας
        while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class=row>';
            echo '<span class="col logs_id">' . $item['id'] . '</span>';
            echo '<span class="col logs_message">' . $item['message'] . '</span>';
            echo '<span class="col logs_ip">' . $item['ip'] . '</span>';
            echo '<span class="col logs_user">' . $item['user_name'] . '</span>';
            echo '<span class="col logs_date">' . date('Y-m-d H:i:s', strtotime($item['log_date'])) . '</span>';
            echo '<span class="col logs_browser">' . $item['browser'] . '</span>';
            echo '</div>';

        }

        echo '</div>';

        $stmt->closeCursor();
        $stmt = null;

    }



}



?>