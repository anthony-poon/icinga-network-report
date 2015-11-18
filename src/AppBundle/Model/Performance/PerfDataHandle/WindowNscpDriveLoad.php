<?php
namespace AppBundle\Model\Performance\PerfDataHandle;

//Hard code to Gb first
class WindowNscpDriveLoad {
    public function parseData($rawDataArray){
        $outputArray = array();
        foreach ($rawDataArray as $timestamp => $dataArray) {
            preg_match("/Used Space'=([0-9.]+)(\w+);.+;([0-9.]+)$/", $dataArray["perfdata"], $match);
            if (!empty($match)) {
                $usedSpace = $match[1];
                $totalSpace = $match[3];
            } else {
                //Should throw some error later
                $usedSpace = $dataArray["output"];
                $totalSpace = $dataArray["output"];
            }
            $outputArray[$timestamp]["Used Space(GB)"] = $usedSpace;
            $outputArray[$timestamp]["Total Space(GB)"] = $totalSpace;
        }
        return $outputArray;
    }
    public function parseDataDayAverage($rawDataArray) {
        $outputArray = array();
        $data = $this->parseData($rawDataArray);
        foreach ($data as $timestamp => $dataSet) {
            preg_match("/^([0-9-]+) ([0-9:]+)$/", $timestamp, $match);
            $date = $match[1];
            $tempArray[$date]["Used Space(GB)"][] = $dataSet["Used Space(GB)"];
            $tempArray[$date]["Total Space(GB)"][] = $dataSet["Total Space(GB)"];
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
