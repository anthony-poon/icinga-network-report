<?php

namespace AppBundle\DbObject\DefinedHost;

use AppBundle\DbObject\AbstractDefinedHost;

class Window232Disk1 extends AbstractDefinedHost{
    //put your code here
    public function getOrder() {
        return 6;
    }
    public function getDisplayName() {
        return "232 Server - C: Drive";
    }
    public function getHostId(){
        return 287;
    }
    protected function getAvailabilityId(){
        return array(385);
        //return array(85);
    }
}
