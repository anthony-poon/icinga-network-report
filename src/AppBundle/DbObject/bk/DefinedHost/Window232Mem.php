<?php
namespace AppBundle\DbObject\DefinedHost;

use AppBundle\DbObject\AbstractDefinedHost;

class Window232Mem extends AbstractDefinedHost{
    //put your code here
    public function getOrder() {
        return 7;
    }
    public function getDisplayName() {
        return "232 Server - Memory";
    }
    public function getHostId(){
        return 287;
    }
    protected function getAvailabilityId(){
        return array(314);
        //return array(85);
    }
}
