<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Model\Performance\PerfDataHandle;

use DateTime;
use DatePeriod;
use DateInterval;

class HandleBaseClass {
    protected $dateArray;
    function setPeriod($startDate, $endDate) {
        $period = new DatePeriod($startDate, new DateInterval('P1D'), $endDate);
        foreach ($period as $date) {
            $this->dateArray[] = $date->format("Y-m-d");
        }
    }
}
