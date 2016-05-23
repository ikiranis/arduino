<?php

/**
 * File: Arduino.php
 * Created by rocean
 * Date: 20/05/16
 * Time: 22:02
 */



class Arduino
{
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
    
            while($sensor=$stmt->fetch(PDO::FETCH_ASSOC))
            {

                $somerandomnumber=rand(1,3);

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