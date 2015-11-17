<?php
namespace AppBundle\DbObject;

use DateTime;
use DateInterval;
use DatePeriod;
use PDO;
use AppBundle\Model\NotificationNode;
use AppBundle\Model\AvailabilityNode;

abstract class AbstractDefinedHost extends Host{
    //put your code here
    abstract protected function getOrder();
    abstract public function getHostId();
    abstract protected function getAvailabilityId();
    public function __construct() {
        parent::__construct(new IcingaDbConnector());
        $this->objId = $this->getHostId();
        $this->queryService();
        $statment = "SELECT host_object_id, display_name, address FROM ".self::$prefix."hosts WHERE (host_object_id = :object_id)";
        $query = self::$dbConnection->prepare($statment);
        $query->bindValue(':object_id', $this->objId, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $this->objName = $result["display_name"];
        $this->ipV4 = $result["address"];
    }
    
    public function shouldIncludeInExcel() {
        return true;
    }
    
    public function getDisplayName() {
        return $this->objName;
    }
    
    public function getIsActive() {
        return true;
    }
    
    public function getAvailability($startDate, $endDate) {
        if (empty($this->getAvailabilityId())) {
            throw new Exception("Missing ID to check for availability. Please override getAvailabilityId and return an array of ID.");
        }
        if ($endDate > new DateTime()) {
            $endDate = new DateTime();
        }
        $queryStr = "";
        for ($i = 0; $i < count($this->getAvailabilityId()); $i ++) {
            $queryStr = $queryStr.":id_".$i;
            if ($i !== count($this->getAvailabilityId())-1) {
                $queryStr .= ",";
            }
        }
        $statment = "SELECT object_id, notification_type, notification_reason, output, state, start_time FROM ".self::$prefix."notifications WHERE (object_id IN ($queryStr)) AND (DATE(start_time) BETWEEN :start_date AND :end_date) ORDER BY start_time";
        $query = self::$dbConnection->prepare($statment);
        $idQuery = implode(",", $this->getAvailabilityId());
        for ($i = 0; $i < count($this->getAvailabilityId()); $i ++) {
            $query->bindValue(':id_'.$i, $this->getAvailabilityId()[$i]);
        }
        $query->bindValue(':start_date', $startDate->format("Y-m-d"));
        $query->bindValue(':end_date', $endDate->format("Y-m-d"));
        $query->execute();
        $availability = new AvailabilityNode($startDate, $endDate);
        $availability->setDisplayName($this->getDisplayName());
        $availability->setOrder($this->getOrder());
        while ($result = $query->fetch(PDO::FETCH_ASSOC)) {
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $result["start_time"]);
            $notification = new NotificationNode();
            $notification->setId($result["object_id"]);
            $notification->setMessage($result["output"]);
            $notification->setDate($date);
            $notification->setName(self::$dbConnection->queryObjName($result["object_id"]));                
            $notification->setTime($result["start_time"]);
            $notification->setState($result["state"]);
            $availability->addNotification($date, $notification);
        }
        return $availability;
    }
    
}
