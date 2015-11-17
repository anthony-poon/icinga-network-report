<?php
namespace AppBundle\DbObject\DefinedHost;

use AppBundle\DbObject\AbstractDefinedHost;

class Window232Cpu extends AbstractDefinedHost{
    //put your code here
    public function getOrder() {
        return 6;
    }
    public function getDisplayName() {
        return "232 Server - CPU";
    }
    public function getHostId(){
        return 287;
    }
    protected function getAvailabilityId(){
        return array(312);
        //return array(85);
    }
}
