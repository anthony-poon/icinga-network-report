<?php
namespace AppBundle\DbObject\PerfDataHandle;
class PingWindowHandle {
    public function parseData($rawDataArray){       
        $outputArray = array();
        foreach ($rawDataArray as $timestamp => $dataArray) {
            preg_match("/rta=(.+)ms.+pl=(.+)%/", $dataArray["perfdata"], $match);
            if (!empty($match)) {
                $roundTripTime = $match[1];
            } else {
                //Should throw some error later
                $roundTripTime = $dataArray["output"];
            }
            $outputArray[$timestamp]["Round Trip Time (ms)"] = $roundTripTime;
        }
        return $outputArray;

    }
    public function parseDataDayAverage($rawDataArray) {
        $outputArray = array();
        $data = $this->parseData($rawDataArray);
        foreach ($data as $timestamp => $dataSet) {
            preg_match("/^([0-9-]+) ([0-9:]+)$/", $timestamp, $match);
            $date = $match[1];
            $tempArray[$date]["Round Trip Time (ms)"][] =$dataSet["Round Trip Time (ms)"];
        }
        foreach ($tempArray as $datestamp => $tempDataSetArray) {
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
