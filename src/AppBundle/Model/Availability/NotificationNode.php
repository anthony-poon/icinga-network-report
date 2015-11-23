<?php
namespace AppBundle\Model\Availability;

use AppBundle\DbObject\IcingaDbConnector;
use DateTime;

class NotificationNode {
    //put your code here
    private $output;
    private static $idoDb;
    public function __construct($object_id, $startDate, $endDate) {
        $this->output = array();
        $statment = "SELECT notification_id, object_id, notification_type, notification_reason, output, state, start_time FROM ".IcingaDbConnector::DB_PREFIX."notifications WHERE (object_id = :object_id) AND (DATE(start_time) BETWEEN :start_date AND :end_date) ORDER BY start_time";
        if (empty(self::$idoDb)) {
            self::$idoDb = new IcingaDbConnector();
        }
        $query = self::$idoDb->prepare($statment);
        $query->bindValue(':object_id', $object_id);
        $query->bindValue(':start_date', $startDate->format("Y-m-d"));
        $query->bindValue(':end_date', $endDate->format("Y-m-d"));
        $query->execute();
        while ($result = $query->fetch(IcingaDbConnector::FETCH_ASSOC)) {
            $temp = array();
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $result["start_time"])->format("Y-m-d");
            $temp["id"] = $result["notification_id"];
            $temp["message"] = $result["output"];
            $temp["name"] = self::$idoDb->queryObjName($result["object_id"]);                
            $temp["time"] = $result["start_time"];
            $temp["state"] = $result["state"];
            $this->output[$date][] = $temp;
        }
    }
    
    public function getOutputArray(){
        return $this->output;
    }
}
