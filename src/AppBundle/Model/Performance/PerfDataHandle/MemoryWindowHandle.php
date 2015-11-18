<?php
namespace AppBundle\Model\Performance\PerfDataHandle;
class MemoryWindowHandle {
    public function parseData($rawDataArray){
        $outputArray = array();
        foreach ($rawDataArray as $timestamp => $dataArray) {
            preg_match("/=([0-9.]+)Mb.+;([0-9.]+)/", $dataArray["perfdata"], $match);
            if (!empty($match)) {
                $memoryUsage = $match[1];
                $totalUsage = $match[2];
            } else {
                //Should throw some error later
                $memoryUsage = $dataArray["output"];
                $totalUsage = $dataArray["output"];
            }
            
            $outputArray[$timestamp]["Memory usage(MB)"] = $memoryUsage;
            $outputArray[$timestamp]["Total Memory(MB)"] = $totalUsage;
        }
        return $outputArray;
    }
    public function parseDataDayAverage($rawDataArray) {
        $outputArray = array();
        $data = $this->parseData($rawDataArray);
        foreach ($data as $timestamp => $dataSet) {
            preg_match("/^([0-9-]+) ([0-9:]+)$/", $timestamp, $match);
            $date = $match[1];
            $tempArray[$date]["Memory usage(MB)"][] = $dataSet["Memory usage(MB)"];
            $tempArray[$date]["Total Memory(MB)"][] = $dataSet["Total Memory(MB)"];
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
