<?php

/**
 * File: Arduino.php
 * Created by rocean
 * Date: 20/05/16
 * Time: 22:02
 */


class Arduino
{

    // Επιστρέφει τις τελευταίες θερμοκρασίες σε array
    static function getAvgLastTemperatures() {
        $conn = new RoceanDB();
        $conn->CreateConnection();

        $sql = 'SELECT round(avg(LastItems.probe1)) as avg1, 
                round(avg(LastItems.probe2)) as avg2,
                round(avg(LastItems.probe3)) as avg3,
                round(avg(LastItems.probe4)) as avg4,
                round(avg(LastItems.probe5)) as avg5,
                round(avg(LastItems.probeCPU)) as avg6
                FROM (SELECT * FROM data ORDER BY time desc limit 1,12) LastItems';

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
    static function getLastTemperatures() {
        $conn = new RoceanDB();
        $conn->CreateConnection();

        $sql = 'SELECT * FROM data ORDER BY time DESC';
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

        $sql = 'SELECT * FROM data ORDER BY time DESC';
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
        $conn->CreateConnection();

        $sql = 'SELECT * FROM sensors';
        $stmt = RoceanDB::$conn->prepare($sql);

        $stmt->execute();

        // TODO να θέτει μηδενικές αρχικές τιμές αν δεν υπάρχει καμιά εγγραφή, όπως κάνει στα alerts

        ?>
            <div class="ListTable">
            
            

        <?php


                while($item=$stmt->fetch(PDO::FETCH_ASSOC))
                {
                ?>

                    <div class="SensorsRow" id="SensorID<?php echo $item['id']; ?>">
                        <form class="table_form sensors_form" id="sensors_formID<?php echo $item['id']; ?>">
                        <span class="ListColumn"><input class="input_field"
                                                        placeholder="<?php echo __('sensors_room'); ?>"
                                                        title="<?php echo __('valid_room'); ?>"
                                                        pattern='^[a-zA-ZΆ-Ϋά-ώ][a-zA-ZΆ-Ϋά-ώ0-9-_\.]{2,15}$'
                                                        maxlength="15" required type="text" id="room" name="room"
                                                        value="<?php echo $item['room']; ?>"></span>
                        <span class="ListColumn"><input class="input_field"
                                                        placeholder="<?php echo __('sensors_sensor'); ?>"
                                                        title="<?php echo __('valid_sensor'); ?>"
                                                        pattern='^[a-zA-ZΆ-Ϋά-ώ ][a-zA-ZΆ-Ϋά-ώ0-9- _\.]{2,20}$'
                                                        maxlength="20"  type="text"  id="sensor_name" name="sensor_name" value="<?php echo $item['sensor_name']; ?>"></span>
                        <span class="ListColumn"><input class="input_field"
                                                        placeholder="<?php echo __('sensors_dbfield'); ?>"
                                                        title="<?php echo __('valid_dbfield'); ?>"
                                                        pattern='^[a-zA-Z][a-zA-Z0-9-_\.]{2,20}$'
                                                        maxlength="20" required type="text"  id=="db_field" name="db_field" value="<?php echo $item['db_field']; ?>"></span>

                        <input type="button" class="update_button button_img" name="update_sensor" title="<?php echo __('update_row'); ?>" onclick="updateSensor(<?php echo $item['id']; ?>);">

                        <input type="button" class="delete_button button_img" name="delete_sensor" title="<?php echo __('delete_row'); ?>" onclick="deleteSensor(<?php echo $item['id']; ?>);">

                        <span class="message" id="messageID<?php echo $item['id']; ?>"></span>
                        </form>
                    </div>

                    <?php
                }
                ?>

            </div>
            <input type="button" name="insert_sensor" onclick="insertSensor();" value="<?php echo __('insert_row'); ?>">
            
        <?php

        $stmt->closeCursor();
        $stmt = null;

    }

    // Εμφάνιση των εγγραφών των power σε μορφή form fields για editing
    static function getPowerInFormFields () {
        $conn = new RoceanDB();
        $conn->CreateConnection();

        $sql = 'SELECT * FROM power';
        $stmt = RoceanDB::$conn->prepare($sql);

        $stmt->execute();

        // TODO να θέτει μηδενικές αρχικές τιμές αν δεν υπάρχει καμιά εγγραφή, όπως κάνει στα alerts

        ?>
            <div class="ListTable">

                


        <?php


                while($item=$stmt->fetch(PDO::FETCH_ASSOC))
                {
                    ?>
                    <div class="PowersRow" id="PowerID<?php echo $item['id']; ?>">
                        <form class="table_form powers_form" id="powers_formID<?php echo $item['id']; ?>">
                        <span class="ListColumn"><input class="input_field"
                                                        placeholder="<?php echo __('power_room'); ?>"
                                                        title="<?php echo __('valid_room'); ?>"
                                                        pattern='^[a-zA-ZΆ-Ϋά-ώ][a-zA-ZΆ-Ϋά-ώ0-9-_\.]{2,15}$'
                                                        maxlength="15" required type="text" name="room" value="<?php echo $item['room']; ?>"></span>
                        <span class="ListColumn"><input class="input_field"
                                                        placeholder="<?php echo __('power_switch'); ?>"
                                                        title="<?php echo __('valid_power_name'); ?>"
                                                        pattern='^[a-zA-ZΆ-Ϋά-ώ ][a-zA-ZΆ-Ϋά-ώ0-9- _\.]{2,20}$'
                                                        maxlength="20" required type="text" name="power_name" value="<?php echo $item['power_name']; ?>"></span>
                        
                        <input type="button" class="update_button button_img" name="update_power" title="<?php echo __('update_row'); ?>" onclick="updatePower(<?php echo $item['id']; ?>);"">
 
                        <input type="button" class="delete_button button_img" name="delete_power" title="<?php echo __('delete_row'); ?>" onclick="deletePower(<?php echo $item['id']; ?>);"">
 
                        <span class="message" id="messagePowerID<?php echo $item['id']; ?>"></span>
                        </form>

                    </div>
                    <?php
                }
                ?>

            </div>
            <input type="button" name="insert_power" onclick="insertPower();" value="<?php echo __('insert_row'); ?>">

        <?php

        $stmt->closeCursor();
        $stmt = null;

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

                    <input type="button" class="delete_button button_img" name="delete_user" title="<?php echo __('delete_row'); ?>" onclick="deleteUser(<?php echo $item['user_id']; ?>);"">

                    <span class="message" id="messageUserID<?php echo $item['user_id']; ?>"></span>
                    </form>
                </div>
                <?php
            }
            ?>

        </div>

        <?php
        if($UserGroupID==1) {  // Αν είναι admin ο user εμφάνισε κουμπί για προσθήκη νέου user
            ?>

            <input type="button" name="insert_user" onclick="insertUser();" value="<?php echo __('insert_row'); ?>">
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
        $alerts=$conn->getTableArray('alerts', null, 'user_id=?', array($userID));  // Παίρνει τα δεδομένα του πίνακα alerts σε array
        $sensors=$conn->getTableArray('sensors');   // Παίρνει τα δεδομένα του πίνακα sensors σε array

        if(empty($alerts)) {  // Αν δεν επιστρέψει κανένα αποτέλεσμα, σετάρουμε εμείς μια πρώτη γραμμή στο array
            $alerts[]=array('id'=>'0', 'email'=>'', 'time_limit'=>'', 'temp_limit'=>'', 'sensors_id'=>'', 'user_id'=>'');
        }

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

                    <input type="button" class="delete_button button_img" name="delete_alert" title="<?php echo __('delete_row'); ?>" onclick="deleteAlert(<?php echo $alert['id']; ?>);"">

                    <span class="message" id="messageAlertID<?php echo $alert['id']; ?>"></span>
                    </form>
                </div>
                <?php
            }
            ?>

        
        </div>
        <input type="button" name="insert_alert" onclick="insertAlert();" value="<?php echo __('insert_row'); ?>">

        <?php


    }


    static function getPowerStatus($id) {

        $conn = new RoceanDB();
        $conn->CreateConnection();

        $sql = 'SELECT status FROM power WHERE id=?';
        $stmt = RoceanDB::$conn->prepare($sql);

        $stmt->execute(array($id));


        if($item=$stmt->fetch(PDO::FETCH_ASSOC))
        {

            return $item['status'];

        }

        $stmt->closeCursor();
        $stmt = null;

    }

    // Επιστρέφει τα id και db_fields σε array από το sensor table
    static function getSensorsArray() {
        $conn = new RoceanDB();
        $conn->CreateConnection();

        $sql = 'SELECT id, db_field FROM sensors';
        $stmt = RoceanDB::$conn->prepare($sql);

        $stmt->execute();
        $result=$stmt->fetchAll();
        
        return $result;

        $stmt->closeCursor();
        $stmt = null;

    }
    
    // TODO Να δω τι άλλο μπορεί να έχει το dashboard
    // TODO Να εμφανίζει alerts που έχουν γίνει. Και όλα τα άλλα πεδία του dashboard
    static function showDashboard () {
        ?>
        <h2><?php echo __('nav_item_1'); ?></h2>

        <p><?php echo __('system_time'); echo date('Y-m-d H:i:s',time()); ?></p>
        <p><?php echo __('system_temperature'); echo '99&deg; C'?></p>
        <p><?php echo __('system_status');?><span id="dbstatus"></span></p>
 


        <script type="text/javascript">

            setInterval(function(){
                checkIfMysqlIsAlive('#dbstatus');

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

                $somerandomnumber=rand(1,3);

                $sensorsIDArray[]= $sensor['id'];

                switch ($somerandomnumber) {
                    case '1': $temp_diff=' cold'; $dif_text='&#x21E9'; break;
                    case '2': $temp_diff=' warm'; $dif_text='&#x21E7'; break;
                    case '3': $temp_diff=' equal'; $dif_text='-'; break;
                }
                

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

            $sensors=$conn->getTableArray('sensors');   // Παίρνει τα δεδομένα του πίνακα sensors σε array

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
        

        <?php

    }

    static function showConfiguration () {
        $conn = new RoceanDB();
        $UserGroup=$conn->getUserGroup($conn->getSession('username'));  // Παίρνει το user group στο οποίο ανήκει ο χρήστης

        ?>
        <h2><?php echo __('nav_item_5'); ?></h2>


        <?php

        if($UserGroup==1) {  // Αν ο χρήστης είναι admin
        ?>

            <details>
                <summary><?php echo __('settings_sensors'); ?></summary>
                <?php Arduino::getSensorsInFormFields() ?>
            </details>

            <details>
                <summary><?php echo __('settings_power'); ?></summary>
                <?php Arduino::getPowerInFormFields() ?>
            </details>

        <?php
        }
        ?>

        <details>
            <summary><?php echo __('settings_users'); ?></summary>
            <?php Arduino::getUsersInFormFields() ?>
        </details>


        <details>
            <summary><?php echo __('settings_alerts'); ?></summary>
                <?php Arduino::getAlertsInFormFields () ?>
        </details>

        <?php

    }

    static function showLogs () {
        ?>
        <h2><?php echo __('nav_item_6'); ?></h2>
        <?php

            $conn =  new RoceanDB();
            $conn->CreateConnection();

            $sql='SELECT * FROM data ORDER BY time DESC LIMIT 0,100';

            $stmt = RoceanDB::$conn->prepare($sql);

            $stmt->execute();

            echo '<div class=row>';
                echo '<span class=col>id</span>';
                echo '<span class=col>time</span>';
                echo '<span class=col>probe1</span>';
                echo '<span class=col>probe2</span>';
                echo '<span class=col>probe3</span>';
                echo '<span class=col>probe4</span>';
                echo '<span class=col>probe5</span>';
                echo '<span class=col>probeCPU</span>';
            echo '</div>';


            // Αν ο χρήστης username βρεθεί. Αν υπάρχει δηλαδή στην βάση μας
            while($item=$stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class=row>';
                    echo '<span class=col>'.$item['id'].'</span>';
                    echo '<span class=col>'.date('H:i:s',strtotime($item['time'])).'</span>';
                    echo '<span class=col>'.$item['probe1'].'&deg; C</span>';
                    echo '<span class=col>'.$item['probe2'].'&deg; C</span>';
                    echo '<span class=col>'.$item['probe3'].'&deg; C</span>';
                    echo '<span class=col>'.$item['probe4'].'&deg; C</span>';
                    echo '<span class=col>'.$item['probe5'].'&deg; C</span>';
                    echo '<span class=col>'.$item['probeCPU'].'&deg; C</span>';
                echo '</div>';

            }

        $stmt->closeCursor();
        $stmt = null;

    }



}



?>