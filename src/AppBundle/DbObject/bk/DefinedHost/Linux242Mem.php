<?php
namespace AppBundle\DbObject\DefinedHost;

use AppBundle\DbObject\AbstractDefinedHost;

class Linux242Mem extends AbstractDefinedHost{
    //put your code here
    public function getOrder() {
        return 10;
    }
    public function getDisplayName() {
        return "242 Server - Memory";
    }
    
    public function getHostId(){
        return 148;
    }
    protected function getAvailabilityId(){
        return array(376);
    }
}
