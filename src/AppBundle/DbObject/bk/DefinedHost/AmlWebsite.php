<?php
namespace AppBundle\DbObject\DefinedHost;

use AppBundle\DbObject\AbstractDefinedHost;

class AmlWebsite extends AbstractDefinedHost{
    //put your code here
    public function getOrder() {
        return 17;
    }
    public function getDisplayName() {
        return "AML Main Site";
    }
    public function getHostId(){
        return 346;
    }
    protected function getAvailabilityId(){
        return array(346);
    }
}
