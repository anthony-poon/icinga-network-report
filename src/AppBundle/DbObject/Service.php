<?php
namespace AppBundle\DbObject;

use PDO;
use stdClass;
use Exception;

class Service {
    private static $prefix;
    private static $dbConnection;
    public $objId;
    public $history = array();
    public function __construct(&$dbConnection) {
        self::$prefix = IcingaDbConnector::DB_PREFIX;
        self::$dbConnection = $dbConnection;
    }
    
    public function queryHistory() {
        if (self::$dbConnection){ 
            $statment = "SELECT * FROM ".self::$prefix."statehistory WHERE object_id = :object_id ORDER BY state_time";
            $query = self::$dbConnection->prepare($statment);
            $query->bindValue(':object_id', $this->objId, PDO::PARAM_INT);
            $query->execute();
            while ($result = $query->fetch(PDO::FETCH_OBJ)) {
                $history = new stdClass;
                $history->objId = $result->statehistory_id;
                $history->time = $result->state_time;
                $history->output = $result->output;
                $this->history[] = $history;
            }
            return $this->history;
        } else {
            throw new Exception("PDO Connection not set");
        }
    }
    
    public function queryHistoryByPeriod($startDate, $endDate) {
        if (self::$dbConnection){ 
            $statment = "SELECT * FROM ".self::$prefix."statehistory WHERE (object_id = :object_id) AND (state_time BETWEEN :start_date AND :end_date) ORDER BY state_time";
            $query = self::$dbConnection->prepare($statment);
            $query->bindValue(':object_id', $this->objId, PDO::PARAM_INT);
            $query->bindValue(':start_date', $startDate);
            $query->bindValue(':end_date', $endDate);
            $query->execute();
            while ($result = $query->fetch(PDO::FETCH_OBJ)) {
                $history = new stdClass;
                $history->objId = $result->statehistory_id;
                $history->time = $result->state_time;
                $history->output = $result->output;
                $this->history[] = $history;
            }
            return $this->history;
        } else {
            throw new Exception("PDO Connection not set");
        }
    }
}
