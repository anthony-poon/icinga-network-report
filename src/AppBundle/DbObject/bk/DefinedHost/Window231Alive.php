<?php
namespace AppBundle\DbObject\DefinedHost;

use AppBundle\DbObject\AbstractDefinedHost;

class Window231Alive extends AbstractDefinedHost{
    //put your code here
    public function getOrder() {
        return 2;
    }
    public function getDisplayName() {
        return "231 Server - Alive";
    }
    public function getHostId(){
        return 293;
    }
    protected function getAvailabilityId(){
        return array(293);
        //return array(85);
    }
}
