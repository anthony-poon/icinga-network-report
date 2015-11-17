<?php
namespace AppBundle\DbObject;

use PDO;
use Exception;

class Host {
    protected static $prefix;
    protected static $dbConnection;
    public $service = array();
    public $objId;
    public $objName;
    public $os;
    public $hostname;
    public $ipV4;
    public function __construct(&$dbConnection) {
        self::$prefix = IcingaDbConnector::DB_PREFIX;
        self::$dbConnection = $dbConnection;
    }
    
    public function queryService() {
        if (self::$dbConnection) {
            $statment = "SELECT * FROM ".self::$prefix."services WHERE host_object_id = :host_object_id";            
            $query = self::$dbConnection->prepare($statment);
            $query->bindValue(':host_object_id', $this->objId, PDO::PARAM_INT);
            $query->execute();
            while ($result = $query->fetch(PDO::FETCH_OBJ)) {
                $service = new Service(self::$dbConnection);
                $service->objId = $result->service_object_id;
                $service->objName = $result->display_name;
                $service->checkInterval = $result->check_interval;
                $this->service[$service->objName] = $service;
            }
            return $this->service;
        } else {
            throw new Exception("PDO Connection not set");
        }
    }
    
    
}
