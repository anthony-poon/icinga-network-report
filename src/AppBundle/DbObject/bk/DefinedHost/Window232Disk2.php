<?php

namespace AppBundle\DbObject\DefinedHost;

use AppBundle\DbObject\AbstractDefinedHost;

class Window232Disk2 extends AbstractDefinedHost{
    //put your code here
    public function getOrder() {
        return 6;
    }
    public function getDisplayName() {
        return "232 Server - D: Drive";
    }
    public function getHostId(){
        return 287;
    }
    protected function getAvailabilityId(){
        return array(386);
        //return array(85);
    }
}
