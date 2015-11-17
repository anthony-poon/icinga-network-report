<?php
namespace AppBundle\View\ExcelPrinter;

use PHPExcel;
use PHPExcel_IOFactory;
class AvailabilityPrinter {
    const START_COL = 0;
    const START_ROW = 1;
    private $debugMode = 0;
    private $content;
    private $startDate;
    private $endDate;
    private $numberOfAvailabilityNode;
    
    public function __construct($data, $startDate, $endDate) {
        $this->content = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->numberOfAvailabilityNode = count($data);
        $this->workbook = new PHPExcel();
        $this->workbook->removeSheetByIndex(0);
    }
    
    public function setDebugMode($mode) {
        $this->debugMode = $mode;
    }    
    
    public function build() {
        $this->printAvailabilityTable();
    }
    
    private function printAvailabilityTable(){
        $sheet = $this->workbook->createSheet();
        $sheet->setTitle("Availability");
        $row = self::START_ROW;
        $col = self::START_COL;
        $sheet->setCellValueByColumnAndRow($col , $row++, "Availability Report");
        $row = $row + 2;
        $dateRange = new \DatePeriod($this->startDate, new \DateInterval("P1D"), $this->endDate);
        foreach ($dateRange as $date) {
            $sheet->setCellValueByColumnAndRow($col , $row++, $date->format("Y-m-d"));
        }
        $detailArray = array();
        foreach ($this->content as $availabilityNode) {
            $col = $col + 1;
            $row = 3;
            $sheet->setCellValueByColumnAndRow($col , $row++, $availabilityNode->getDisplayName());
            foreach($availabilityNode->getAllnotification() as $date => $notificationArray) {
                if ($availabilityNode->getState($date) === 0) {
                    $sheet->setCellValueByColumnAndRow($col , $row++, "OK");
                } else {
                    $msgArray = array();
                    $strLength = 0;
                    foreach ($notificationArray as $notification) {
                        $detailArray[$availabilityNode->getDisplayName()][] = $notification;
                        $msg = $notification->getTime()." - ".$availabilityNode->getDisplayName().": ".$notification->getMessage();
                        $msgArray[] = $msg;
                        if (strlen($msg) > $strLength) {
                            $strLength = strlen($msg);
                        }
                    }
                    $allNotificationMsg = implode("\n", $msgArray);
                    $sheet->setCellValueByColumnAndRow($col , $row, "Down");
                    $cell = $sheet->getCellByColumnAndRow($col , $row);
                    $coordStr = $cell->getCoordinate();
                    $sheet->getComment($coordStr)->getText()->createTextRun($allNotificationMsg);
                    $sheet->getComment($coordStr)->setWidth(($strLength*7)."px");
                    $height = count($notificationArray)*25;
                    if ($height > 300) {
                        $height = 300;
                    }
                    $sheet->getComment($coordStr)->setHeight($height."px");
                    $row++;
                } 
            }
        }
        foreach ($detailArray as $name => $detail) {
            $this->printNotificationDetail($name, $detail);
        }
        for ($i = 0; $i <= $this->numberOfAvailabilityNode + self::START_COL; $i ++) {
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(TRUE);
        }
    }
    
    private function printNotificationDetail($hostname, $detailArray) {
        $sheet = $this->workbook->createSheet();
        $hostname = preg_replace("/\s/", "_", $hostname);
        $hostname = preg_replace("/:/", "", $hostname);
        $sheet->setTitle($hostname."_Detail");
        $row = self::START_ROW;
        $col = self::START_COL;
        $sheet->setCellValueByColumnAndRow($col , $row++, $hostname." Notifications");
        $row++;
        $sheet->setCellValueByColumnAndRow($col++ , $row, "Timestamp");
        $sheet->setCellValueByColumnAndRow($col++ , $row, "Object Name");
        $sheet->setCellValueByColumnAndRow($col++ , $row++, "Message");
        foreach ($detailArray as $detail) {
            $col = self::START_COL;
            $sheet->setCellValueByColumnAndRow($col++ , $row, $detail->getTime());
            $sheet->setCellValueByColumnAndRow($col++ , $row, $hostname);
            $sheet->setCellValueByColumnAndRow($col++ , $row, $detail->getMessage());
            $row++;
        }
        for ($i = 0; $i <= count($detailArray) + self::START_COL; $i ++) {
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(TRUE);
        }
    }
    
    public function echoContent() {
        $writer = PHPExcel_IOFactory::createWriter($this->workbook, 'Excel2007');
        $writer->save('php://output');
    }
}
