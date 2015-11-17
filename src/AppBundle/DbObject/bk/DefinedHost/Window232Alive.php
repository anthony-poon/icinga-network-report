<?php
namespace AppBundle\DbObject\DefinedHost;

use AppBundle\DbObject\AbstractDefinedHost;

class Window232Alive extends AbstractDefinedHost{
    //put your code here
    public function getOrder() {
        return 5;
    }
    public function getDisplayName() {
        return "232 Server - Alive";
    }
    public function getHostId(){
        return 287;
    }
    protected function getAvailabilityId(){
        return array(287);
        //return array(85);
    }
}
