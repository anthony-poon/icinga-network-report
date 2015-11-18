<?php
namespace AppBundle\Model\Performance\PerfDataHandle;

class LinuxDiskLoadHandle {
    //put your code here
    public function parseData($rawDataArray){
        $outputArray = array();
        foreach ($rawDataArray as $timestamp => $dataArray) {
            preg_match("/([\\d]+) MB .+$/", $dataArray["output"], $match);
            if (!empty($match)) {
                $availSpace = $match[1];
            } else {
                //Should throw some error later
                $availSpace = $dataArray["output"];
            }
            preg_match("/;(\\d+)$/", $dataArray["perfdata"], $match);
            if (!empty($match)) {
                $totalSpace = $match[1];
            } else {
                //Should throw some error later
                $totalSpace = $dataArray["output"];
            }
            
            $outputArray[$timestamp]["Available Space (MB)"] = $availSpace;
            $outputArray[$timestamp]["Total Space (MB)"] = $totalSpace;
        }
        return $outputArray;
    }
    public function parseDataDayAverage($rawDataArray) {
        $outputArray = array();
        $data = $this->parseData($rawDataArray);
        foreach ($data as $timestamp => $dataSet) {
            preg_match("/^([0-9-]+) ([0-9:]+)$/", $timestamp, $match);
            $date = $match[1];
            $tempArray[$date]["Available Space (MB)"][] = $dataSet["Available Space (MB)"];
            $tempArray[$date]["Total Space (MB)"][] = $dataSet["Total Space (MB)"];
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
