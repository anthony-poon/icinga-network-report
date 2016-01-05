<?php
namespace AppBundle\View\ExcelPrinter;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use DateTime;
class AvailabilityPrinter {
    const START_COL = 0;
    const START_ROW = 1;
    const COMMENT_MAX_HEIGHT = 300;
    private $debugMode = 0;
    private $count;
    private $startDate;
    private $endDate;
    
    public function __construct($serializedData) {
        $this->debugMode = $serializedData["debugMode"];
        $this->printData = $serializedData["printData"];
        $this->startDate = DateTime::createFromFormat("Y-m-d H:i:s", $serializedData["startDate"]);
        $this->endDate = DateTime::createFromFormat("Y-m-d H:i:s", $serializedData["endDate"]);
        $this->count = $serializedData["count"];
        $this->workbook = new PHPExcel();
        $this->workbook
                ->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->workbook->removeSheetByIndex(0);
    }
    
    public function build(){
        $sheet = $this->workbook->createSheet();
        $sheet->setTitle("Availability");
        $row = self::START_ROW;
        $col = self::START_COL;
        $sheet->setCellValueByColumnAndRow($col , $row++, "Availability Report");
        $row = $row + 1;
        $sheet->setCellValueByColumnAndRow($col , $row++, "Object Id");
        $sheet->setCellValueByColumnAndRow($col , $row++, "Object Name");
        $dateRange = new \DatePeriod($this->startDate, new \DateInterval("P1D"), $this->endDate);
        foreach ($dateRange as $date) {
            $sheet->setCellValueByColumnAndRow($col , $row++, $date->format("Y-m-d"));
        }

        foreach ($this->printData as $availabilityNode) {
            $col = $col + 1;
            $row = 3;
            $sheet->setCellValueByColumnAndRow($col , $row++, $availabilityNode["availObjId"]);
            $sheet->setCellValueByColumnAndRow($col , $row++, $availabilityNode["displayName"]);
            foreach($availabilityNode["printData"] as $dateString => $notificationArray) {
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
                    switch ($availabilityNode["state"][$dateString]) {
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
                    if ($height > self::COMMENT_MAX_HEIGHT) {
                        $height = self::COMMENT_MAX_HEIGHT;
                    }
                    $sheet->getComment($coordStr)->setHeight($height."px");
                    $row++;
                } 
            }
        }
        foreach ($this->printData as $availabilityNode) {
            $this->printNotificationDetail($availabilityNode);
        }
        for ($i = 0; $i <= $this->count + self::START_COL; $i ++) {
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(TRUE);
        }
    }
    
    private function printNotificationDetail($availabilityNode) {
        if (!$availabilityNode["isEmpty"]) {
            $sheet = $this->workbook->createSheet();
            $escapedName = preg_replace("/\s/", "_", $availabilityNode["displayName"]);
            $escapedName = preg_replace("/:/", "", $escapedName);
            $sheet->setTitle($escapedName."_Detail");
            $row = self::START_ROW;
            $col = self::START_COL;
            $sheet->setCellValueByColumnAndRow($col , $row++, $escapedName);
            $row++;
            $sheet->setCellValueByColumnAndRow($col++ , $row, "Notification Id");
            $sheet->setCellValueByColumnAndRow($col++ , $row, "Timestamp");
            $sheet->setCellValueByColumnAndRow($col++ , $row, "Object Name");
            $sheet->setCellValueByColumnAndRow($col++ , $row, "Message");
            $sheet->setCellValueByColumnAndRow($col++ , $row++, "State Type");
            $maxCol = 0;
            foreach ($availabilityNode["printData"] as $dateString => $notificationArray) {
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
