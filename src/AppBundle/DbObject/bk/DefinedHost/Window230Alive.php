<?php
namespace AppBundle\DbObject\DefinedHost;

use AppBundle\DbObject\AbstractDefinedHost;

class Window230Alive extends AbstractDefinedHost{
    //put your code here
    public function getOrder() {
        return 1;
    }
    public function getDisplayName() {
        return "230 Server - Alive";
    }
    public function getHostId(){
        return 216;
    }
    protected function getAvailabilityId(){
        return array(216);
        //return array(85);
    }
}
