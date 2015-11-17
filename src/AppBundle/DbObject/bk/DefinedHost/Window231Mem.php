<?php
namespace AppBundle\DbObject\DefinedHost;

use AppBundle\DbObject\AbstractDefinedHost;

class Window231Mem extends AbstractDefinedHost{
    //put your code here
    public function getOrder() {
        return 4;
    }
    public function getDisplayName() {
        return "231 Server - Memory";
    }
    
    public function getHostId(){
        return 293;
    }
    protected function getAvailabilityId(){
        return array(353);
        //return array(85);
    }
}
