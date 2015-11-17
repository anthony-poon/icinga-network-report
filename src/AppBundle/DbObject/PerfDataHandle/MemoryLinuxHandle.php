<?php
namespace AppBundle\DbObject\PerfDataHandle;

class MemoryLinuxHandle {
    //put your code here
    public function parseData($rawDataArray){
        $outputArray = array();
        foreach ($rawDataArray as $timestamp => $dataArray) {
            preg_match("/Total: ([\\d]+) MB - Used: ([\\d]+) MB/", $dataArray["output"], $match);
            if (!empty($match)) {
                $totalMemory = $match[1];
                $usedMemory = $match[2];
            } else {
                //Should throw some error later
                $totalMemory = $dataArray["output"];
                $usedMemory = $dataArray["output"];
            }
            $outputArray[$timestamp]["Memory usage(MB)"] = $usedMemory;
            $outputArray[$timestamp]["Total Memory(MB)"] = $totalMemory;
        }
        return $outputArray;
    }
    public function parseDataDayAverage($rawDataArray) {
        $outputArray = array();
        $tempArray = array();
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
