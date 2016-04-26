<?php
/**
 * File: Session.php
 * Created by rocean
 * Date: 21/04/16
 * Time: 20:14
 * Sessions handler class
 * Based on Source code and more info from http://php.net/manual/en/function.session-set-save-handler.php
 */

require_once ('RoceanDB.php');

class SysSession implements SessionHandlerInterface
{

    public static $lifetime;
    public $default_lifetime=30*60; // minutes*seconds
    public $default_saved_lifetime=60*60*24*30; // seconds * minutes * hoursofday * daysofmonth
    
    function setLifetime($lifetime) {
        self::$lifetime=$lifetime;
    }

    public function open($savePath, $sessionName)
    {

        $conn = new RoceanDB();
        $conn->CreateConnection();


        $sql='SELECT Timeout FROM Session WHERE Session_Id = ? AND (Session_Time+Timeout) > ?';

        $stmt = RoceanDB::$conn->prepare($sql);

        $stmt->execute(array(session_id(),date('Y-m-d H:i:s')));

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            self::$lifetime=$row['Timeout'];
        }else{
            self::$lifetime=$this->default_lifetime;
        }
        

        if($conn){
            return true;
        }else{
            return false;
        }
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
        $conn = new RoceanDB();
        $conn->CreateConnection();

        $sql='SELECT Session_Data,Timeout FROM Session WHERE Session_Id = ? AND ((UNIX_TIMESTAMP(Session_Time)+Timeout) > ?)';


        $stmt = RoceanDB::$conn->prepare($sql);

        $stmt->execute(array($id,date('Y-m-d H:i:s')));

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            return $row['Session_Data'];
            self::$lifetime=$row['Timeout'];
        }else{
            return "";
            self::$lifetime=$this->default_lifetime;
        }
    }

    public function write($id, $data)
    {
        $conn = new RoceanDB();
        $conn->CreateConnection();


        $sql='SELECT * FROM Session WHERE Session_Id = ?';


        $stmt = RoceanDB::$conn->prepare($sql);

        $stmt->execute(array($id));

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)){

            if($row['Timeout']==self::$lifetime)
            {
                $sql='UPDATE Session SET Session_Time=?, Session_Data=? WHERE Session_Id=?';
                $params=array(date('Y-m-d H:i:s'),$data, $id);
            }
            else {
                $sql='UPDATE Session SET Session_Time=?, Timeout=?, Session_Data=? WHERE Session_Id=?';
                $params=array(date('Y-m-d H:i:s'),self::$lifetime,$data,$id);
            }



            $stmt = RoceanDB::$conn->prepare($sql);

            $stmt->execute($params);

 
        }else{
            $sql='INSERT INTO Session (Session_Id, Session_Time, Session_Data, Timeout) VALUES (?,?,?,?)';

            $stmt = RoceanDB::$conn->prepare($sql);

            $stmt->execute(array($id,date('Y-m-d H:i:s'),$data,self::$lifetime));
        }




        if($stmt){
            return true;
        }else{
            return false;
        }
    }

    public function destroy($id)
    {

        $conn = new RoceanDB();
        $conn->CreateConnection();


        $sql='DELETE FROM Session WHERE Session_Id =?';

        $stmt = RoceanDB::$conn->prepare($sql);


        $stmt->execute(array($id));

        if($stmt){
            return true;
        }else{
            return false;
        }
    }

    public function gc($maxlifetime)
    {
        $conn = new RoceanDB();
        $conn->CreateConnection();

        $sql='DELETE FROM Session WHERE ((UNIX_TIMESTAMP(Session_Time)+Timeout) < ?)';

        $stmt = RoceanDB::$conn->prepare($sql);

        $stmt->execute(array(time()));


        if($stmt){
            return true;

        }else{
            return false;

        }
    }

 

}

/**
 * Garbage Collector
 * @see session.gc_divisor      100
 * @see session.gc_maxlifetime 1440
 * @see session.gc_probability    1
 * @usage execution rate 1/100
 *        (session.gc_probability/session.gc_divisor)
 * Πιθανότητα για να τρέξει η gc()
 */

ini_set('session.gc_maxlifetime',1);
ini_set('session.gc_divisor',100);
ini_set('session.gc_probability',10);

$handler = new SysSession();
session_set_save_handler($handler, true);
$default_lifetime=$handler->default_lifetime;
$default_saved_lifetime=$handler->default_saved_lifetime;




