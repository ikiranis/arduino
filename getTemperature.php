<?php
/**
 * File: getTemperature.php
 * Created by rocean
 * Date: 23/05/16
 * Time: 01:29
 */

require_once('libraries/common.inc.php');


    echo json_encode(Arduino::getLastTemperatures()); // στέλνει το array σε json στην javascript



?>