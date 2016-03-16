<?php

namespace AppBundle\View\ExcelPrinter;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Cell;
use PHPExcel_Chart_DataSeriesValues;
use PHPExcel_Chart_Title;
use PHPExcel_Chart_DataSeries;
use PHPExcel_Chart_PlotArea;
use PHPExcel_Chart_Legend;
use PHPExcel_Chart;

class ExcelTable {
    private $topLeft;
    private $colStart;
    private $rowStart;
    private $startCoord;
    private $width;
    private $height;
    private $colLabel;
    private $rowLabel;
    private $tableRow;
    private $debugMode = 0;
    const DEBUG_OFF = 0;
    const DEBUG_SHOW_ALL = 1;
    public function __construct($col ,$row) {
        $this->topLeft = "";
        $this->colStart = $col;
        $this->rowStart = $row;
        $this->startCoord = PHPExcel_Cell::stringFromColumnIndex($col).(string)$row;
        $this->colLabel = array();
        $this->rowLabel = array();
        $this->tableRow = array();
        $this->width = 0;
        $this->height = 0;
    }
    public function setColLabel($labelArray) {
        if (!empty($labelArray)){
            $this->topLeft = "Date";
            for ($i = 0; $i < count($labelArray); $i ++) {
                if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/", $labelArray[$i], $match)){
                    $labelArray[$i] = $match[2]."-".$match[3]." ".$match[4].":".$match[5];
                    $this->topLeft = "Date (YYYY/MM/DD HH:MM)";
                } else if (preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $labelArray[$i], $match)){
                    $labelArray[$i] = $match[1]."-".$match[2]."-".$match[3];
                    $this->topLeft = "Date (YYYY/MM/DD)";
                }
            }
            $this->colLabel = array_merge(array($this->topLeft), $labelArray);
            if (count($this->colLabel) > $this->width) {
                $this->width = count($this->colLabel);
            }
        }
    }
    
    public function setRowLabel($labelArray) {
        if (!empty($labelArray)){
            $this->rowLabel = $labelArray;
            //This will pad the array if table row is added first then label is set
            array_pad($this->rowLabel, $this->height, "");
            
            //This will run if number of label is larger than number of record
            if (count($this->rowLabel) > $this->height) {
                $this->height = count($this->rowLabel);
            }
        }
    }
    
    public function setTopLeft($str) {
        $this->topLeft = $str;
    }
    
    public function addTableRow($recordArray) {
        foreach ($recordArray as &$cellValue) {
            if (!preg_match("/^[0-9.%]+$/", $cellValue)) {
                $cellValue = "";
            }
        }
        
        $this->tableRow[] = $recordArray;
        
        //This will run if number of record is more than label.
        if (count($this->rowLabel) < count($this->tableRow)) {
            $this->rowLabel[] = "";
        }
        
        //Update height if number of record > previous height
        if (count($this->tableRow) + 1 > $this->height) {
            $this->height = count($this->tableRow) + 1;
        }
        
        //Update width if number of node + label is > than width
        if (count($recordArray) + 1 > $this->width) {
            $this->width = count($recordArray) + 1;
        }
    }
    
    public function getTabelArray() {
        $output[] = $this->colLabel;
        for ($i = 0; $i < count($this->rowLabel); $i++) {
            $output[] = array_merge(array($this->rowLabel[$i]), $this->tableRow[$i]);
        }
        return $output;
    }
    
    public function getColLabelRange() {
        $labelColStart = $this->colStart + 1;
        $str = "$".PHPExcel_Cell::stringFromColumnIndex($labelColStart)."$".(string)$this->rowStart.":";
        $endCoord = "$".PHPExcel_Cell::stringFromColumnIndex($this->colStart + $this->width - 2)."$".(string)($this->rowStart);
        return $str.$endCoord;
    }
    
    public function getDataLabelArray() {
        $output = array();
        $col = $this->colStart + 1;
        for ($i = 1; $i <= count($this->rowLabel); $i++) {
            $output[] = "$".PHPExcel_Cell::stringFromColumnIndex($this->colStart)."$".(string)($this->rowStart + $i);
        }
        return $output;
    }
    
     public function getDataRange() {
        $output = array();
        $col = $this->colStart + 1;
        $row = $this->rowStart + 1;
        for ($i = 0; $i < $this->height - 1; $i++) {
            $str = "$".PHPExcel_Cell::stringFromColumnIndex($col)."$".(string)($row + $i).":";
            $str = $str."$".PHPExcel_Cell::stringFromColumnIndex($col +  $this->width - 2)."$".(string)($row + $i);
            $output[] = $str;
        }
        return $output;
    }
    
    public function getWidth() {
        return $this->width;
    }
    
    public function getHeight(){
        return $this->height;
    }
    
    public function getTopLeft() {
        return $this->startCoord;
    }
}

class PerfDataPrinter {
    private $row;
    private $col;
    private $isVerbal;
    const TABLE_START_COL = 0;
    const TABLE_START_ROW = 1;
    const CHART_START_COL = 0;
    const CHART_MAX_WIDTH = 0;
    const CHART_HEIGHT = 15;
    const TABLE_TO_CHART_SPACING = 5;
    //put your code here
    public function __construct($dataObjArray, $option) {
        $this->isVerbal = $option["isVerbal"];
        $this->workbook = new PHPExcel();
        $this->workbook->removeSheetByIndex(0);
        foreach ($dataObjArray as &$hostObj) {
            $this->printDetailPage($hostObj);    
        }
    }
    
    private function printDetailPage($host){
        $sheet = $this->workbook->createSheet();
        $illegalChar = array("*", ":", "/", "\\", "?", "[", "]");
        
        $sheetName = str_replace($illegalChar, "", $host->getDisplayName());
        $sheetName = str_replace(" ", "_", $sheetName);
        $sheet->setTitle($sheetName);
        $allData = $host->getData();
        if (!empty($allData)) {
            $table = new ExcelTable(self::TABLE_START_COL, self::TABLE_START_ROW);
            $row = array();
            foreach ($allData as $date => $dataSet) {
                
                foreach ($dataSet as $dataName => $dataValue) {
                    $row[$dataName][$date] = $dataValue;
                }
            }
            $table->setColLabel(array_keys($allData));
            $table->setRowLabel(array_keys($row));
            foreach ($row as $tableRecord) {
                $table->addTableRow($tableRecord);
            }
            $sheet->fromArray($table->getTabelArray(), NULL, $table->getTopLeft(), TRUE);
            
            for ($i = 0; $i <= (self::TABLE_START_COL + $table->getWidth()); $i ++) {
                $sheet->getColumnDimensionByColumn($i)->setAutoSize(TRUE);
            }
            
            $chart = $this->getChartObj($sheetName, $table, $host->getDisplayName());
            $chart->setTopLeftPosition(PHPExcel_Cell::stringFromColumnIndex(self::CHART_START_COL).(string)($table->getHeight() + 3));
            $chartWidth = $table->getWidth();
            if (($chartWidth >= self::CHART_MAX_WIDTH) && (0 !== self::CHART_MAX_WIDTH)) {
                $chartWidth = self::CHART_MAX_WIDTH;
            }
            $chart->setBottomRightPosition(PHPExcel_Cell::stringFromColumnIndex($chartWidth + 1).
                    (string)($table->getHeight() + self::CHART_HEIGHT + self::TABLE_TO_CHART_SPACING));
            $sheet->addChart($chart);
        }
    }

    private function printSummary(&$sheet, $perfDataArray) {
        $timestampArray = array_keys($perfDataArray);
        $dataSetArray = array_values($perfDataArray);
        $this->col++;
        foreach ($timestampArray as $timestamp) {
            $sheet->setCellValueByColumnAndRow($this->col++, $this->row, $timestamp);
        }
        $this->col = 1;
        $this->row++;
        $tableFirstRow = $this->row;
        foreach ($dataSetArray[0] as $dataName => $data) {
            $sheet->setCellValueByColumnAndRow($this->col, $this->row++, $dataName);
        }
        foreach ($dataSetArray as $dataSet) {
            $this->col++;
            $this->row = $tableFirstRow;
            foreach ($dataSet as $data) {
                if (preg_match("/^[0-9.%]+$/", $data)) {
                    $sheet->setCellValueByColumnAndRow($this->col, $this->row++, $data);
                } else {
                    $sheet->setCellValueByColumnAndRow($this->col, $this->row++, "-");
                }
            }
        }
    }
    
    private function getChartObj($sheetname, $table, $chartName) {
        $dataseriesLabels = array();
        $dataRange = $table->getDataLabelArray();
        foreach ($dataRange as $range) {
            //$rowStr = (string)($tableStartRow + $i);
            //$dataseriesLabels[] = new PHPExcel_Chart_DataSeriesValues('String', $sheetName.'!$'.$startColChar.'$'.$rowStr, NULL, 1);            
            $dataseriesLabels[] = new PHPExcel_Chart_DataSeriesValues('String', $sheetname.'!'.$range, count($dataRange));
        }
        
        $xAxisTickValues = array(
            new PHPExcel_Chart_DataSeriesValues("String", $sheetname.'!'.$table->getColLabelRange(), NULL, 1)
        );
        $dataRange = $table->getDataRange();
        $dataSeriesValues = array();
        foreach ($dataRange as $range) {
            //$row = (string) ($tableStartRow + $i);
            //$dataSeriesValues[] = new PHPExcel_Chart_DataSeriesValues("Number", $sheetName."!$".$dataStartColChar."$".$row.":$".$endColChar."$".$row, NULL, count($data));
            $dataSeriesValues[] = new PHPExcel_Chart_DataSeriesValues("Number", $sheetname."!".$range, NULL, count($dataRange));
        }

        $series = new PHPExcel_Chart_DataSeries(
                PHPExcel_Chart_DataSeries::TYPE_LINECHART, // plotType
                PHPExcel_Chart_DataSeries::GROUPING_STANDARD, // plotGrouping
                range(count($dataSeriesValues)-1, 0), // plotOrder
                $dataseriesLabels, // plotLabel
                $xAxisTickValues, // plotCategory
                $dataSeriesValues        // plotValues
        );
        
        $plotarea = new PHPExcel_Chart_PlotArea(NULL, array($series));
        $legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_TOPRIGHT, NULL, false);

        $title = new PHPExcel_Chart_Title($chartName);
        $chart = new PHPExcel_Chart(
                'chart1', // name
                $title, // title
                $legend, // legend
                $plotarea, // plotArea
                true, // plotVisibleOnly
                0, // displayBlanksAs
                NULL, // xAxisLabel
                NULL  // yAxisLabel
        );

        return $chart;
    }
    
    public function echoContent() {
        $writer = PHPExcel_IOFactory::createWriter($this->workbook, 'Excel2007');
        $writer->setIncludeCharts(TRUE);
        $writer->save('php://output');
    }

}

