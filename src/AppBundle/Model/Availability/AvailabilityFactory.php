<?php
namespace AppBundle\Model\Availability;
use AppBundle\DbObject\IcingaDbConnector;
use AppBundle\DbObject\ReportDbConnector;
use PDO;
use DateTime;
use JsonSerializable;


class AvailabilityFactory implements JsonSerializable{
    //put your code here
    private static $reportDb;
    private static $idoDb;
    private $allObj;
    private $startDate;
    private $endDate;
    public function __construct($startDate, $endDate) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        if ($this->endDate > new DateTime()) {
            $this->endDate = new DateTime();
        }
        self::$reportDb = new ReportDbConnector();
        self::$idoDb = new IcingaDbConnector();
        $statement = "SELECT `avail_obj_id`, `display_name`, `order` FROM ".ReportDbConnector::DB_PREFIX."avail_obj_definition WHERE is_active = 1 ORDER BY `order`";
        $query = self::$reportDb->prepare($statement);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $mappingStatement = "SELECT `service_id` FROM ".ReportDbConnector::DB_PREFIX."avail_mapping WHERE avail_obj_id = :avail_obj_id";
            $mappingQuery = self::$reportDb->prepare($mappingStatement);
            $mappingQuery->bindValue(":avail_obj_id", $row["avail_obj_id"]);
            $mappingQuery->execute();
            $mappingResult = $mappingQuery->fetchAll(PDO::FETCH_ASSOC);
            foreach ($mappingResult as $mappingRow) {
                $row["service_id"][] = $mappingRow["service_id"];
            }
            $this->allObj[$row["avail_obj_id"]] = $row;
        }
    }
    
    public function jsonSerialize() {
        $output = array();
        foreach ($this->allObj as $availObjId => $queryObj) {
            $availability = new AvailabilityNode($availObjId, $queryObj["service_id"], $this->startDate, $this->endDate);
            $availability->setDisplayName($queryObj["display_name"]);
            $availability->setOrder($queryObj["order"]);
            $output[] = $availability->jsonSerialize();
        }
        $metaData = array(
            "count" => count($output),
            "startDate" => $this->startDate->format("Y-m-d H:i:s"),
            "endDate" => $this->endDate->format("Y-m-d H:i:s")            
        );
        return array_merge($metaData, array("printData" => $output));
    }
}
