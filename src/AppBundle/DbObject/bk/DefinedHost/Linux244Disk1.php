<?php
namespace AppBundle\DbObject\DefinedHost;

use AppBundle\DbObject\AbstractDefinedHost;

class Linux244Disk1 extends AbstractDefinedHost{
    //put your code here
    public function getOrder() {
        return 16;
    }
    public function getDisplayName() {
        return "244 Server Disk 1";
    }
    
    public function getHostId(){
        return 278;
    }
    protected function getAvailabilityId(){
        return array(356);
    }
}
