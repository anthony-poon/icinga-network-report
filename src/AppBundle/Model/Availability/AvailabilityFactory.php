<?php
namespace AppBundle\Model\Availability;
use AppBundle\DbObject\IcingaDbConnector;
use AppBundle\DbObject\ClassicDbConnector;
use PDO;
use DateTime;

class AvailabilityFactory {
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
        self::$reportDb = new ClassicDbConnector();
        self::$idoDb = new IcingaDbConnector();
        $statement = "SELECT `avail_obj_id`, `display_name`, `order` FROM ".ClassicDbConnector::DB_PREFIX."avail_obj_definition WHERE is_active = 1 ORDER BY `order`";
        $query = self::$reportDb->prepare($statement);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $mappingStatement = "SELECT `service_id` FROM ".ClassicDbConnector::DB_PREFIX."avail_mapping WHERE avail_obj_id = :avail_obj_id";
            $mappingQuery = self::$reportDb->prepare($mappingStatement);
            $mappingQuery->bindValue(":avail_obj_id", $row["avail_obj_id"]);
            $mappingQuery->execute();
            $mappingResult = $mappingQuery->fetch(PDO::FETCH_ASSOC);
            $row["service_id"] = $mappingResult["service_id"];
            $this->allObj[$row["avail_obj_id"]] = $row;
        }
    }
    
    public function getAvailability() {
        $output = array();
        foreach ($this->allObj as $objectId => $queryObj) {
            $statment = "SELECT object_id, notification_type, notification_reason, output, state, start_time FROM ".IcingaDbConnector::DB_PREFIX."notifications WHERE (object_id = :object_id) AND (DATE(start_time) BETWEEN :start_date AND :end_date) ORDER BY start_time";
            $query = self::$idoDb->prepare($statment);
            $query->bindValue(':object_id', $queryObj["service_id"]);
            $query->bindValue(':start_date', $this->startDate->format("Y-m-d"));
            $query->bindValue(':end_date', $this->endDate->format("Y-m-d"));
            $query->execute();
            $availability = new AvailabilityNode($this->startDate, $this->endDate);
            $availability->setDisplayName($queryObj["display_name"]);
            $availability->setOrder($queryObj["order"]);
            while ($result = $query->fetch(PDO::FETCH_ASSOC)) {
                $date = DateTime::createFromFormat('Y-m-d H:i:s', $result["start_time"]);
                $notification = new NotificationNode();
                $notification->setId($result["object_id"]);
                $notification->setMessage($result["output"]);
                $notification->setDate($date);
                $notification->setName(self::$idoDb->queryObjName($result["object_id"]));                
                $notification->setTime($result["start_time"]);
                $notification->setState($result["state"]);
                $availability->addNotification($date, $notification);
            }
            $output[] = $availability;
        }
        return $output;
    }
}
