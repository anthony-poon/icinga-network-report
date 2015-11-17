<?php
namespace AppBundle\DbObject\DefinedHost;

use AppBundle\DbObject\AbstractDefinedHost;

class Linux242Disk2 extends AbstractDefinedHost{
    //put your code here
    public function getOrder() {
        return 12;
    }
    public function getDisplayName() {
        return "242 Server Disk 2";
    }
    public function getHostId(){
        return 148;
    }
    protected function getAvailabilityId(){
        return array(155);
    }
}
