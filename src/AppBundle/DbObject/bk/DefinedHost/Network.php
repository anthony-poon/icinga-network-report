<?php
namespace AppBundle\DbObject\DefinedHost;

use AppBundle\DbObject\AbstractDefinedHost;

class Network extends AbstractDefinedHost{
    //put your code here
    public function getOrder() {
        return 0;
    }
    public function getDisplayName() {
        return "Ping to 8.8.8.8";
    }
    
    public function getHostId(){
        return 173;
    }
    protected function getAvailabilityId(){
        return array(172);
    }
}
