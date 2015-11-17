<?php
namespace AppBundle\DbObject\DefinedHost;

use AppBundle\DbObject\AbstractDefinedHost;

class Linux242HostAlive extends AbstractDefinedHost{
    //put your code here
    public function getOrder() {
        return 8;
    }
    public function getDisplayName() {
        return "242 Server - Alive";
    }
    
    public function getHostId(){
        return 148;
    }
    protected function getAvailabilityId(){
        return array(148);
    }
}
