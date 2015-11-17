<?php
namespace AppBundle\DbObject\DefinedHost;

use AppBundle\DbObject\AbstractDefinedHost;

class Linux242Disk1 extends AbstractDefinedHost{
    //put your code here
    public function getOrder() {
        return 11;
    }
    public function getDisplayName() {
        return "242 Server Disk 1";
    }
    public function getHostId(){
        return 148;
    }
    protected function getAvailabilityId(){
        return array(154);
    }
}
