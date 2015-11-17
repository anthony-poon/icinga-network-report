<?php
namespace AppBundle\DbObject\PerfDataHandle;

class CpuLinuxHandle {
    public function parseData($rawDataArray){
        $outputArray = array();
        foreach ($rawDataArray as $timestamp => $dataArray) {
            preg_match("/load1=([0-9.]+);.+load5=([0-9.]+).+load15=([0-9.]+);/", $dataArray["perfdata"], $match);
            if (!empty($match)) {
                $load1Min = $match[1];
                $load5Min = $match[2];
                $load15Min = $match[3];
            } else {
                //Should throw some error later
                $load1Min = $dataArray["perfdata"];
                $load5Min = $dataArray["perfdata"];
                $load15Min = $dataArray["perfdata"];
            }
            $outputArray[$timestamp]["1 min avg Load"] = $load1Min;
            $outputArray[$timestamp]["5 min avg Load"] = $load5Min;
            $outputArray[$timestamp]["15 min avg Load"] = $load15Min;
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
            $tempArray[$date]["1 min avg Load"][] = $dataSet["1 min avg Load"];
            $tempArray[$date]["5 min avg Load"][] = $dataSet["5 min avg Load"];
            $tempArray[$date]["15 min avg Load"][] = $dataSet["15 min avg Load"];
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
