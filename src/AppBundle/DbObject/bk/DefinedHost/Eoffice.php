<?php
namespace AppBundle\DbObject\DefinedHost;

use AppBundle\DbObject\AbstractDefinedHost;

class Eoffice extends AbstractDefinedHost{
    //put your code here
    public function getOrder() {
        return 18;
    }
    public function getHostId(){
        return 148;
    }
    
    public function getDisplayName() {
        return "Eoffice";
    }
    
    protected function getAvailabilityId(){
        return array(331);
    }
}
