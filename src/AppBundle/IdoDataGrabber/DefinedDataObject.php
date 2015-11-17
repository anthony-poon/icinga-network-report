<?php

class IcingaDbConnector extends PDO{
    const DB_ENGI = "mysql";
    const DB_NAME = "icinga2idomysql";
    const DB_USER = "icinga2-ido-mysq";
    const DB_PASS = "pc20011196";
    const DB_HOST = "127.0.0.1";
    const DB_PORT = "3306";
    const DB_PREFIX = "icinga_";
    public function __construct() {
        parent::__construct(self::DB_ENGI.":dbname=".self::DB_NAME.";host=".self::DB_HOST, self::DB_USER, self::DB_PASS);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}

class ClassicDbConnector extends PDO{
    const DB_ENGI = "mysql";
    const DB_NAME = "ido_reporting";
    const DB_USER = "ido_reporting";
    const DB_PASS = "6c3FUJTArYtyEPQG";
    const DB_HOST = "127.0.0.1";
    const DB_PORT = "3306";
    const DB_PREFIX = "report_";
    public function __construct() {
        parent::__construct(self::DB_ENGI.":dbname=".self::DB_NAME.";host=".self::DB_HOST, self::DB_USER, self::DB_PASS);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}


class DefinedDataObject {
    private $id;
    private static $idoConnector;
    private static $dbConnector;
    public function __construct($id) {
        self::$idoConnector = new IcingaDbConnector();
        self::$dbConnector = new ClassicDbConnector();
        $this->id = $id;
    }
    public function getServiceId(){
        return $this->id;
    }
    private function getFromIdo(){
        $id = $this->getServiceId();
        $serviceStatement = "SELECT service_object_id, status_update_time, output, perfdata, current_state FROM ".IcingaDbConnector::DB_PREFIX."servicestatus WHERE service_object_id = :service_object_id";
        $serviceQuery = self::$idoConnector->prepare($serviceStatement);
        $serviceQuery->bindValue(":service_object_id", $id);
        $serviceQuery->execute();
        $serviceResult = $serviceQuery->fetch(PDO::FETCH_ASSOC);
        $hostStatement = "SELECT host_object_id FROM ".IcingaDbConnector::DB_PREFIX."services WHERE service_object_id = :service_object_id";
        $hostQuery = self::$idoConnector->prepare($hostStatement);
        $hostQuery->bindValue(":service_object_id", $serviceResult["service_object_id"]);
        $hostQuery->execute();
        $hostResult = $hostQuery->fetch(PDO::FETCH_ASSOC);
        return array_merge($hostResult, $serviceResult);

    }
    private function insertRecord($dataArray){
        if (empty($dataArray)){
            throw new Exception("Unable to get data for object id:".$this->getServiceId());
        }
        $statement = "INSERT INTO ".ClassicDbConnector::DB_PREFIX."perfdata (host_object_id, service_object_id, status_update_time, output, perfdata, current_state) VALUES (:host_object_id, :service_object_id, :status_update_time, :output, :perfdata, :current_state)";
        $query = self::$dbConnector->prepare($statement);
        $query->bindValue(":host_object_id", $dataArray["host_object_id"]);
        $query->bindValue(":service_object_id", $dataArray["service_object_id"]);
        $query->bindValue(":status_update_time", $dataArray["status_update_time"]);
        $query->bindValue(":output", $dataArray["output"]);
        $query->bindValue(":perfdata", $dataArray["perfdata"]);
        $query->bindValue(":current_state", $dataArray["current_state"]);
        $query->execute();
    }
    
    public function grabData() {
        $rawData = $this->getFromIdo();
        $this->insertRecord($rawData);
    }
    
}
