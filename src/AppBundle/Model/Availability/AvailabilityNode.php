<?php
namespace AppBundle\Model\Availability;

class AvailabilityNode {
    //put your code here
    private $startDate;
    private $endDate;
    private $displayName;
    private $order;
    private $allNotification = array();
    private $state = array();
    public function __construct ($startDate, $endDate){
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $dateRange = new \DatePeriod($this->startDate, new \DateInterval("P1D"), $this->endDate);
        foreach ($dateRange as $date) {
            $this->allNotification[$date->format("Y-m-d")] = array(); 
            $this->state[$date->format("Y-m-d")] = 0; 
        }
    }
    public function getStartDate() {
        return $this->startDate;
    }

    public function getEndDate() {
        return $this->endDate;
    }
    
    public function getDisplayName() {
        return $this->displayName;
    }

    public function getOrder() {
        return $this->order;
    }

    public function getAllNotification() {
        return $this->allNotification;
    }

    public function setDisplayName($displayName) {
        $this->displayName = $displayName;
    }

    public function setOrder($order) {
        $this->order = $order;
    }
    
    public function getState($date) {
        return $this->state[$date];
    }
    
    public function addNotification($date, $notification) {
        if ($notification->getState() !== 0) {
            $this->state[$date->format("Y-m-d")] = 1;
        }
        $this->allNotification[$date->format("Y-m-d")][] = $notification;
    }
}
