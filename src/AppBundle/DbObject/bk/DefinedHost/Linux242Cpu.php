<?php
namespace AppBundle\DbObject\DefinedHost;

use AppBundle\DbObject\AbstractDefinedHost;

class Linux242Cpu extends AbstractDefinedHost{
    //put your code here
    public function getOrder() {
        return 9;
    }
    public function getDisplayName() {
        return "242 Server - CPU";
    }
    
    public function getHostId(){
        return 148;
    }
    protected function getAvailabilityId(){
        return array(365);
    }
}
