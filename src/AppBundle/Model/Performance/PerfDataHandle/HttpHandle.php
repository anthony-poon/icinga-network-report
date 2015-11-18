<?php
namespace AppBundle\Model\Performance\PerfDataHandle;

class HttpHandle {
    public function parseData($rawDataArray){
        $outputArray = array();
        foreach ($rawDataArray as $timestamp => $dataArray) {
            preg_match("/HTTP\\/\d.\d (\d{3})/", $dataArray["output"], $match);
            if (!empty($match)) {
                $httpCode = $match[1];
            } else {
                //Should throw some error later
                $httpCode = $dataArray["output"];
            }
            
            $outputArray[$timestamp]["HTTP Code"] = $httpCode;
        }
        return $outputArray;
    }
    public function parseDataDayAverage($rawDataArray) {
        $outputArray = array();
        $data = $this->parseData($rawDataArray);
        foreach ($data as $timestamp => $dataSet) {
            preg_match("/^([0-9-]+) ([0-9:]+)$/", $timestamp, $match);
            $date = $match[1];
            $tempArray[$date][] = $dataSet["HTTP Code"];
        }
        foreach ($tempArray as $date => $arrayOfHttpCode) {
            $errorCount = 0;
            foreach ($arrayOfHttpCode as $httpCode) {
                if ($httpCode !== "200") {
                    $errorCount++;
                }
            }
            $outputArray[$date]["Error Count"] = $errorCount;
        }
        return $outputArray;
    }
}
