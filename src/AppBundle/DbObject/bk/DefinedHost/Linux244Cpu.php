<?php
namespace AppBundle\DbObject\DefinedHost;

use AppBundle\DbObject\AbstractDefinedHost;

class Linux244Cpu extends AbstractDefinedHost{
    //put your code here
    public function getOrder() {
        return 14;
    }
    public function getDisplayName() {
        return "244 Server - Cpu";
    }
    
    public function getHostId(){
        return 278;
    }
    protected function getAvailabilityId(){
        return array(366);
    }
}
