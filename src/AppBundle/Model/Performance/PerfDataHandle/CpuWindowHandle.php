<?php
namespace AppBundle\Model\Performance\PerfDataHandle;

use AppBundle\Model\Performance\PerfDataHandle\HandleBaseClass;

class CpuWindowHandle extends HandleBaseClass{
    public function parseData($rawDataArray){
        $outputArray = array();
        foreach ($rawDataArray as $timestamp => $dataArray) {
            preg_match("/=(\\d+)%/", $dataArray["perfdata"], $match);
            if (!empty($match)) {
                $load = $match[1];
            } else {
                //Should throw some error later
                $load = $dataArray["output"];
            }
            $outputArray[$timestamp]["60 min avg Load"] = $load;
        }
        return $outputArray;
    }
    public function parseDataDayAverage($rawDataArray) {
        $outputArray = array();
        foreach ($this->dateArray as $dateString) {
            $tempArray[$dateString]["60 min avg Load"] = array();
        }
        $data = $this->parseData($rawDataArray);
        foreach ($data as $timestamp => $dataSet) {
            preg_match("/^([0-9-]+) ([0-9:]+)$/", $timestamp, $match);
            $date = $match[1];
            $tempArray[$date]["60 min avg Load"][] = $dataSet["60 min avg Load"];
        }
        
        foreach ($tempArray as $datestamp => $tempDataSetArray) {
            $outputArray[$datestamp] = array();
            foreach ($tempDataSetArray as $dataSetName => $allData) {
                $sum = 0;
                $count = 0;
                foreach ($allData as $output) {
                    if (is_numeric($output)) {                    
                        $sum = $sum + $output;
                        $count++;
                    }
                }
                if ($count !== 0) {
                    $outputArray[$datestamp][$dataSetName] = round($sum/$count, 2);
                } else {
                    $outputArray[$datestamp][$dataSetName] = "No Data";
                }
            }            
        }
        return $outputArray;
    }
}
