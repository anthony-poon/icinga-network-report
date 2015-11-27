<?php
namespace AppBundle\Model\Availability;

use AppBundle\DbObject\ReportDbConnector;

class ProblemAcknowledge {
    private $startDate;
    private $endDate;
    private static $db;
    private $acknowArray;
    public function __construct($availObjId, $startDate, $endDate) {
        $acknowArray = array();
        self::$db = new ReportDbConnector();
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $statement = "SELECT t1.acknowledge_id, t1.time_start, t1.time_end, t1.reason, t2.avail_obj_id"
                . " FROM ".ReportDbConnector::DB_PREFIX."avail_acknowledge as t1 JOIN ".ReportDbConnector::DB_PREFIX."avail_acknowledge_mapping as t2"
                . " ON t1.acknowledge_id = t2.acknowledge_id "
                . " WHERE (avail_obj_id = :avail_obj_id) AND (DATE(t1.time_start) BETWEEN DATE(:start_date) AND DATE(:end_date))";
        $query = self::$db->prepare($statement);
        $query->bindValue(":start_date", $startDate->format("Y-m-d"));
        $query->bindValue(":end_date", $endDate->format("Y-m-d"));
        $query->bindValue(":avail_obj_id", $availObjId);
        $query->execute();
        $result = $query->fetchAll(ReportDbConnector::FETCH_ASSOC);
        foreach ($result as $row) {
            $this->acknowArray[] = $row;
        }
    }
    
    public function getOutputArray() {
        return $this->acknowArray;
    }    
}
