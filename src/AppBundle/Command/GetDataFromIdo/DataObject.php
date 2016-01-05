<?php

namespace AppBundle\Command\GetDataFromIdo;

use AppBundle\DbObject\ReportDbConnector;
use AppBundle\DbObject\IcingaDbConnector;
use PDO;

class DataObject {
    private $id;
    private static $idoConnector;
    private static $dbConnector;
    public function __construct($id) {
        self::$idoConnector = new IcingaDbConnector();
        self::$dbConnector = new ReportDbConnector();
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
        $statement = "INSERT INTO ".ReportDbConnector::DB_PREFIX."perfdata (host_object_id, service_object_id, status_update_time, output, perfdata, current_state) VALUES (:host_object_id, :service_object_id, :status_update_time, :output, :perfdata, :current_state)";
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
