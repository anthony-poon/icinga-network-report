<?php
namespace AppBundle\Model\Performance;

use AppBundle\DbObject\ReportDbConnector;
use PDO;

class PerfDataObject {    
    private static $db;
    private $hostId;
    private $rawData;
    private $displayName;
    private $dataArray;
    private $startDate;
    private $isVerbal;
    private $endDate;
    private $serivceId;
    public function __construct($id, $option) {
        self::$db = new ReportDbConnector();
        $this->serivceId = $id;
        $this->startDate = $option["startDate"];
        $this->endDate = $option["endDate"];
        $this->isVerbal = $option["isVerbal"];
        $this->populateRawData($this->hostId);
        $this->populateInfo();
        if (!empty($this->dataHandle) && !empty($this->rawData)) {
            if ($this->isVerbal) {
                $this->dataArray = $this->dataHandle->parseData($this->rawData);
            } else {
                $this->dataArray = $this->dataHandle->parseDataDayAverage($this->rawData);
            }
        }
    }
    private function populateRawData($hostId) {
        $allData = array();
        $statement = "SELECT * FROM ".ReportDbConnector::DB_PREFIX."perfdata WHERE (service_object_id = :service_object_id) AND (DATE(status_update_time) BETWEEN :start_date AND :end_date) GROUP BY `status_update_time`";
        $query = self::$db->prepare($statement);
        $query->bindValue(":service_object_id", $this->serivceId);
        $query->bindValue(":start_date", $this->startDate->format("Y-m-d"));
        $query->bindValue(":end_date", $this->endDate->format("Y-m-d"));
        $query->execute();
        while ($result = $query->fetch(PDO::FETCH_ASSOC)) {
            $data = array();
            $data["output"] = $result["output"];
            if (!empty($result["perfdata"])) {
                $data["perfdata"] = $result["perfdata"];
            } else {
                $data["perfdata"] = "";
            }
            
            $allData[$result["status_update_time"]] = $data;
        }
        $this->rawData = $allData;
    }
    
    private function populateInfo() {
        $statement = "SELECT * FROM ".ReportDbConnector::DB_PREFIX."perf_object_definition WHERE service_id = :service_id";
        $query = self::$db->prepare($statement);
        $query->bindValue(":service_id", $this->serivceId);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $this->displayName = $result["display_name"];
        if (!empty($result["parser_class"])) {
            $className = "AppBundle\\Model\\Performance\\PerfDataHandle\\".$result["parser_class"];
            $this->dataHandle = new $className();
        }
    }
    
    
    public function getData(){
        return $this->dataArray;
    }
    
    public function getDisplayName(){
        return $this->displayName;
    }
}
