<?php
namespace AppBundle\DbObject;

use PDO;

class IcingaDbConnector extends PDO{
    //put your code here
    const DB_ENGI = "mysql";
    const DB_NAME = "icinga2idomysql";
    const DB_USER = "icinga2-ido-mysq";
    const DB_PASS = "TzpVCXwFWBYXxCwQ";
    const DB_HOST = "127.0.0.1";
    const DB_PORT = "3306";
    const DB_PREFIX = "icinga_";
    public function __construct() {
        parent::__construct(self::DB_ENGI.":dbname=".self::DB_NAME.";host=".self::DB_HOST, self::DB_USER, self::DB_PASS);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    public function queryObjName($id) {
        $hostStatment = "SELECT name1, name2 FROM ".self::DB_PREFIX."objects WHERE object_id = :host_object_id";            
        $query = $this->prepare($hostStatment);
        $query->bindValue(':host_object_id', $id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if ($result["name2"]) {
            return $result["name2"];
        } else {
            return $result["name1"];
        }
    }
}
