<?php
namespace AppBundle\IdoDataGrabber;
use PDO;
//use DateTime;

//$date = new DateTime();
//file_put_contents(__DIR__.'/../../../app/logs/data-grabber.log', $date->format("Y-m-d H:i:s")."\n", FILE_APPEND);
require_once __DIR__.'/DefinedDataObject.php';

function getAllDefinedObject() {
    $db = new \ClassicDbConnector();
    $statement = "SELECT service_id FROM report_data_object_definition";
    $query = $db->prepare($statement);
    $query->execute();
    while ($result = $query->fetch(PDO::FETCH_ASSOC)) {
        $Obj = new \DefinedDataObject($result["service_id"]);
        $Obj->grabData();
    }
}
getAllDefinedObject();
