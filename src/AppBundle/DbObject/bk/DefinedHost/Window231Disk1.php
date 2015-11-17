<?php
namespace AppBundle\DbObject\DefinedHost;

use AppBundle\DbObject\AbstractDefinedHost;

class Window231Disk1 extends AbstractDefinedHost{
    //put your code here
    public function getOrder() {
        return 3;
    }
    public function getDisplayName() {
        return "231 Server - C: Drive";
    }
    public function getHostId(){
        return 293;
    }
    protected function getAvailabilityId(){
        return array(384);
        //return array(85);
    }
}
