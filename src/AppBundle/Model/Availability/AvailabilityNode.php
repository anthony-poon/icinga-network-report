<?php
namespace AppBundle\Model\Availability;

use DateTime;
use JsonSerializable;

class AvailabilityNode implements JsonSerializable{
    //put your code here
    private $startDate;
    private $endDate;
    private $isEmpty = true;
    private $displayName;
    private $order;
    private $printData = array();
    private $allAcknowledge;
    private $acknowledgeObj;
    private $availObjId;
    private $state = array();
    public function __construct ($availObjId, $serviceIdArray, $startDate, $endDate){
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->availObjId = $availObjId;
        $dateRange = new \DatePeriod($this->startDate, new \DateInterval("P1D"), $this->endDate);        
        foreach ($dateRange as $dateObj) {
            $this->printData[$dateObj->format("Y-m-d")] = array(); 
            $this->state[$dateObj->format("Y-m-d")] = "0"; 
        }
        $this->acknowledgeObj = new ProblemAcknowledge($availObjId, $this->startDate, $this->endDate);
        $this->allAcknowledge = $this->acknowledgeObj->getOutputArray();
        foreach ($serviceIdArray as $serviceId) {
            $notificationObj = new NotificationNode($serviceId, $this->startDate, $this->endDate);
            $notificationOutput = $notificationObj->jsonSerialize();
            foreach($notificationOutput as $dateString => &$notificationInADay) {                
                if (!empty($notificationInADay)) {
                    $this->isEmpty = false;
                }
                
                foreach ($notificationInADay as &$notification) {
                    if ($notification["state"] !== "0") {
                        if (!empty($this->allAcknowledge)) {
                            foreach ($this->allAcknowledge as $acknowledge) {
                                $startDateObj = DateTime::createFromFormat("Y-m-d H:i:s", $acknowledge["time_start"]);
                                $endDateObj = DateTime::createFromFormat("Y-m-d H:i:s", $acknowledge["time_end"]);                                
                                $notificationTimestampObj = DateTime::createFromFormat("Y-m-d H:i:s", $notification["time"]);
                                if (($startDateObj <= $notificationTimestampObj) && ($endDateObj >= $notificationTimestampObj)) {                                    
                                    $notification["message"] = " *".$acknowledge["reason"]."* ".$notification["message"];
                                    if ($this->state[$dateString] === "0") {
                                        $this->state[$dateString] = "2";
                                    }
                                } else {
                                    $this->state[$dateString] = "1";
                                }
                            }
                        } else {
                            $this->state[$dateString] = "1";
                        }
                    }
                }
                $this->printData[$dateString] = $notificationInADay;
            }            
            
        }
    }

    public function setDisplayName($displayName) {
        $this->displayName = $displayName;
    }

    public function setOrder($order) {
        $this->order = $order;
    }            
    
    public function jsonSerialize() {
        $metaData = array(
            "startDate" => $this->startDate,
            "endDate" => $this->endDate,
            "displayName" => $this->displayName,
            "order" => $this->order,
            "isEmpty" => $this->isEmpty,
            "state" => $this->state            
        );
        return array_merge($metaData, array("printData" => $this->printData));
    }
}
