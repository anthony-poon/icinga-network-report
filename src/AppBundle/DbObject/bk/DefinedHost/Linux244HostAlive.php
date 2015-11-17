<?php
namespace AppBundle\DbObject\DefinedHost;

use AppBundle\DbObject\AbstractDefinedHost;

class Linux244HostAlive extends AbstractDefinedHost{
    //put your code here
    public function getOrder() {
        return 13;
    }
    public function getDisplayName() {
        return "244 Server - Alive";
    }
    
    public function getHostId(){
        return 278;
    }
    protected function getAvailabilityId(){
        return array(278);
    }
}
