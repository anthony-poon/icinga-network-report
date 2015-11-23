<?php
namespace AppBundle\DbObject;

use PDO;

class ReportDbConnector extends PDO{
    //put your code here
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
    
    public function getAllServiceId() {
        $statement = "SELECT service_id FROM ".self::DB_PREFIX."perf_object_definition WHERE (include_in_excel = 1)";
        $query = $this->prepare($statement);
        $query->execute();
        return array_column($query->fetchAll(PDO::FETCH_ASSOC), "service_id");
    }
    
    public function getServiceIdByHostId($groupId) {
        $statement = "SELECT data_table.service_id FROM ".self::DB_PREFIX."group_mapping AS group_table INNER JOIN ".self::DB_PREFIX."perf_object_definition AS data_table ON (group_table.service_uid = data_table.uid) WHERE (group_table.group_id = :group_id)";
        $query = $this->prepare($statement);        
        $query->bindValue(":group_id", $groupId);
        $query->execute();        
        return array_column($query->fetchAll(PDO::FETCH_ASSOC), "service_id");
    }
    
    public function getAllReportGroupId() {
        $output = array();
        $statement = "SELECT uid, file_name from ".self::DB_PREFIX."group_definition WHERE is_active = 1";
        $query = $this->prepare($statement);
        $query->execute();
        while ($result = $query->fetch(self::FETCH_ASSOC)) {
            $output[$result["uid"]] = $result["file_name"];
        }
        return $output;
    }
}