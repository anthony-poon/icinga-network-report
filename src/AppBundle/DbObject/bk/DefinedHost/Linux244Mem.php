<?php
namespace AppBundle\DbObject\DefinedHost;

use AppBundle\DbObject\AbstractDefinedHost;

class Linux244Mem extends AbstractDefinedHost{
    //put your code here
    public function getOrder() {
        return 15;
    }
    public function getDisplayName() {
        return "244 Server - Memory";
    }
    
    public function getHostId(){
        return 278;
    }
    protected function getAvailabilityId(){
        return array(377);
    }
}
