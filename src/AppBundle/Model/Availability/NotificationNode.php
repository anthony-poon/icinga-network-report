<?php
namespace AppBundle\Model\Availability;

class NotificationNode {
    //put your code here
    private $name;
    private $id;
    private $message;
    private $date;
    private $time;
    private $state;

    public function getName() {
        return $this->name;
    }
    public function getDate() {
        return $this->date;
    }
    public function getTime() {
        return $this->time;
    }
    public function getState() {
        return $this->state;
    }

    public function setState($state) {
        $this->state = $state;
    }

    public function setTime($time) {
        $this->time = $time;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function getId() {
        return $this->id;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setMessage($message) {
        $this->message = $message;
    }


}
