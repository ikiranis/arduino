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
    }

    // Εμφάνιση των εγγραφών των sensors σε μορφή form fields για editing
    static function getSensorsInFormFields () {
        $conn = new RoceanDB();
        $conn->CreateConnection();

        $sql = 'SELECT * FROM sensors';
        $stmt = RoceanDB::$conn->prepare($sql);

        $stmt->execute();


        while($item=$stmt->fetch(PDO::FETCH_ASSOC))
        {
        ?>
            <div class="SensorsRow" id="SensorID<?php echo $item['id']; ?>">
                <input type="text" name="room" value="<?php echo $item['room']; ?>">
                <input type="text" name="sensor_name" value="<?php echo $item['sensor_name']; ?>">
                <input type="text" name="db_field" value="<?php echo $item['db_field']; ?>">
                <button name="update_sensor" onclick="updateSensor(<?php echo $item['id']; ?>);">
                    <?php echo __('update_row'); ?></button>
                <button name="delete_sensor" onclick="deleteSensor(<?php echo $item['id']; ?>);">
                    <?php echo __('delete_row'); ?></button>
                <span id="messageID<?php echo $item['id']; ?>"></span>
            </div>
            <?php
        }
        ?>
        <button name="insert_sensor" onclick="insertSensor();"><?php echo __('insert_row'); ?></button>

        <?php

    }

    // Εμφάνιση των εγγραφών των power σε μορφή form fields για editing
    static function getPowerInFormFields () {
        $conn = new RoceanDB();
        $conn->CreateConnection();

        $sql = 'SELECT * FROM power';
        $stmt = RoceanDB::$conn->prepare($sql);

        $stmt->execute();


        while($item=$stmt->fetch(PDO::FETCH_ASSOC))
        {
            ?>
            <div class="PowersRow" id="PowerID<?php echo $item['id']; ?>">
                <input type="text" name="room" value="<?php echo $item['room']; ?>">
                <input type="text" name="power_name" value="<?php echo $item['power_name']; ?>">
                <button name="update_power" onclick="updatePower(<?php echo $item['id']; ?>);"">
                <?php echo __('update_row'); ?></button>
                <button name="delete_power" onclick="deletePower(<?php echo $item['id']; ?>);"">
                <?php echo __('delete_row'); ?></button>
                <span id="messagePowerID<?php echo $item['id']; ?>"></span>
            </div>
            <?php
        }
        ?>
        <button name="insert_power" onclick="insertPower();"><?php echo __('insert_row'); ?></button>

        <?php

    }


    // Εμφάνιση των εγγραφών των alerts σε μορφή form fields για editing
    static function getAlertsInFormFields () {
        $conn = new RoceanDB();

        $userID=$conn->getUserID($conn->getSession('username'));
        $alerts=$conn->getTableArray('alerts', null, 'user_id=?', array($userID));
        

        foreach ($alerts as $alert)
        {
            ?>
            <div class="AlertsRow" id="AlertID<?php echo $alert['id']; ?>">
                <input type="text" name="email" value="<?php echo $alert['email']; ?>">
                <input type="text" name="time_limit" value="<?php echo $alert['time_limit']; ?>">
                <input type="text" name="temp_limit" value="<?php echo $alert['temp_limit']; ?>">
                <input type="text" name="sensors_id" value="<?php echo $alert['sensors_id']; ?>">
                <input type="hidden" name="user_id" value="<?php echo $userID; ?>">
                <button name="update_alert" onclick="updateAlert(<?php echo $alert['id']; ?>);"">
                <?php echo __('update_row'); ?></button>
                <button name="delete_alert" onclick="deleteAlert(<?php echo $alert['id']; ?>);"">
                <?php echo __('delete_row'); ?></button>
                <span id="messageAlertID<?php echo $alert['id']; ?>"></span>
            </div>
            <?php
        }
        ?>
        <button name="insert_alert" onclick="insertAlert();"><?php echo __('insert_row'); ?></button>

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

    }
    
    
    static function showDashboard () {
        ?>
        <h2><?php echo __('nav_item_1'); ?></h2>

        <p><?php echo __('system_time'); echo date('Y-m-d H:i:s',time()); ?></p>
        <p><?php echo __('system_temperature'); echo '99&deg; C'?></p>
        <p><?php echo __('system_status'); echo 'ON'?></p>
        <p><?php echo __('db_status'); echo 'ON'?></p>
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
                

                echo '<div class="temperature_block'.$temp_diff.'">';
                    echo '<span class=temperature_text><span id=temp'.$counter.'>0</span>&deg; C</span>';
                    echo '<span class=room_text>'.$sensor['room'].'</span>';
                    echo '<span class=sensor_name_text>'.$sensor['sensor_name'].'</span>';
                    echo '<span class=time_text id=time'.$counter.'>'.date('Y-m-d H:i:s',time()).'</span>';
                    echo '<span class=dif_text>'.$dif_text.'</span>';
                echo '</div>';

                $counter++;

            }
            ?>
        
            <!--        Στέλνουμε τα array στην javascript-->
            <script type="text/javascript">
                
                var SensorsIDArray= <?php echo json_encode($sensorsIDArray); ?>;
    
            </script>

    <?php
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

            </script>

        <?php
    }

    static function showStatistics () {
        ?>
        <h2><?php echo __('nav_item_4'); ?></h2>
        <?php

    }

    static function showConfiguration () {
        ?>
        <h2><?php echo __('nav_item_5'); ?></h2>

        <details>
            <summary><?php echo __('settings_sensors'); ?></summary>
            <?php Arduino::getSensorsInFormFields () ?>
        </details>

        <details>
            <summary><?php echo __('settings_power'); ?></summary>
            <?php Arduino::getPowerInFormFields () ?>    
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
        


    }



}



?>