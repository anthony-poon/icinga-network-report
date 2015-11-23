<?php
namespace AppBundle\View\ExcelPrinter;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
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
        $this->workbook
                ->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
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
            foreach($availabilityNode->getPrintData() as $dateString => $notificationArray) {
                if (empty($notificationArray)) {
                    $sheet->setCellValueByColumnAndRow($col , $row++, "OK");
                } else {
                    $msgArray = array();
                    $strLength = 0;                    
                    foreach ($notificationArray as $notification) {
                        //$detailArray[$notification["name"]][] = $notification;
                        $msg = $notification["time"]." - ".$notification["name"].": ".$notification["message"];
                        $msgArray[] = $msg;
                        if (strlen($msg) > $strLength) {
                            $strLength = strlen($msg);
                        }
                    }
                    $allNotificationMsg = implode("\n", $msgArray);
                    switch ($availabilityNode->getState($dateString)) {
                        case "0":
                            $sheet->setCellValueByColumnAndRow($col , $row, "OK");
                            break;
                        case "1":
                            $sheet->setCellValueByColumnAndRow($col , $row, "Down");
                            break;
                        case "2":
                            $sheet->setCellValueByColumnAndRow($col , $row, "Ack");
                            break;
                    }
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
        foreach ($this->content as $availabilityNode) {
            $this->printNotificationDetail($availabilityNode);
        }
        for ($i = 0; $i <= $this->numberOfAvailabilityNode + self::START_COL; $i ++) {
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(TRUE);
        }
    }
    
    private function printNotificationDetail($availabilityNode) {
        if (!$availabilityNode->isEmpty()) {
            $sheet = $this->workbook->createSheet();
            $hostname = preg_replace("/\s/", "_", $availabilityNode->getDisplayName());
            $hostname = preg_replace("/:/", "", $hostname);
            $sheet->setTitle($hostname."_Detail");
            $row = self::START_ROW;
            $col = self::START_COL;
            $sheet->setCellValueByColumnAndRow($col , $row++, $hostname);
            $row++;
            $sheet->setCellValueByColumnAndRow($col++ , $row, "Notification Id");
            $sheet->setCellValueByColumnAndRow($col++ , $row, "Timestamp");
            $sheet->setCellValueByColumnAndRow($col++ , $row, "Object Name");
            $sheet->setCellValueByColumnAndRow($col++ , $row, "Message");
            $sheet->setCellValueByColumnAndRow($col++ , $row++, "State Type");
            $maxCol = 0;
            foreach ($availabilityNode->getPrintData() as $dateString => $notificationArray) {
                foreach ($notificationArray as $notification) {
                    $col = self::START_COL;
                    $sheet->setCellValueByColumnAndRow($col++ , $row, $notification["id"]);
                    $sheet->setCellValueByColumnAndRow($col++ , $row, $notification["time"]);
                    $sheet->setCellValueByColumnAndRow($col++ , $row, $notification["name"]);
                    $sheet->setCellValueByColumnAndRow($col++ , $row, $notification["message"]);
                    $sheet->setCellValueByColumnAndRow($col++ , $row, $notification["state"]);
                    if ($maxCol < $col) {
                        $maxCol = $col;
                    }
                    $row++;
                }
            }
            for ($i = 0; $i <= $maxCol + self::START_COL; $i ++) {
                $sheet->getColumnDimensionByColumn($i)->setAutoSize(TRUE);
            }
        }
    }
    
    public function echoContent() {
        $writer = PHPExcel_IOFactory::createWriter($this->workbook, 'Excel2007');
        $writer->save('php://output');
    }
}
