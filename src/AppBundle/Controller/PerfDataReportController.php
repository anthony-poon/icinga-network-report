<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\DbObject\ClassicDbConnector;
use AppBundle\DbObject\PerfDataObject;
use AppBundle\View\ExcelPrinter\PerfDataPrinter;
use DateTime;
use DateInterval;
use Exception;


class PerfDataReportController extends Controller {

    //put your code here
    private static $db;

    public function __construct() {
        self::$db = new ClassicDbConnector();
    }
    /*
    public function jsonRequest(Request $request) {
        
        $option = array(
            "startDate" => $request->get("start"),
            "endDate" => $request->get("end"),
            "isVerbal" => $request->get("verbal"),
            "groupId" => $request->get("group"),
            "debugMode" => $request->get("debug")
        );
        $json = $this->getJsonData($option);
        if (empty($request->get("debug"))) {
            $response = new Response($json);
            $response->headers->set('Content-Type', 'application/json');
        } else {
            $response = new Response($json);
        }
        return $response;
    }
    
    private function getJsonData($option) {
        $startDate = DateTime::createFromFormat("Y-m-d", $option["startDate"]);
        $endDate = DateTime::createFromFormat("Y-m-d", $option["endDate"]);
        $isVerbal = !empty($option["isVerbal"]) && ($option["isVerbal"] === "1");
        $groupId = $option["groupId"];
        if (empty($startDate)) {
            $startDate = new DateTime();
            if (!$isVerbal) {
                $startDate = $startDate->sub(new DateInterval("P1M"));
            } else {
                $startDate = $startDate->sub(new DateInterval("P2D"));
            }
        }
        if (empty($endDate)) {
            $endDate = new DateTime();
            $endDate = $endDate->add(new DateInterval("P1D"));
        }
        if (empty($groupId)) {
            $allServiceId = self::$db->getAllServiceId();
        } else {
            $allServiceId = self::$db->getServiceIdByHostId($groupId);
            if (empty($allServiceId)) {
                throw new Exception("No service registered in the report, or report group not exist.");
            }
        }
        foreach ($allServiceId as $id) {
            $allDataObj[] = new PerfDataObject($id, [
                "startDate" => $startDate,
                "endDate" => $endDate,
                "isVerbal" => $isVerbal,
                "debugMode" => $option["debugMode"]
            ]);
        }
        $json = json_encode($allDataObj);
        return $json;
    }
    */
    public function getReport(Request $request) {
        $response = new Response();
        $startDate = DateTime::createFromFormat("Y-m-d", $request->get("start"));
        $endDate = DateTime::createFromFormat("Y-m-d", $request->get("end"));
        $isVerbal = !empty($request->get("verbal")) && ($request->get("verbal") === "1");
        $groupId = $request->get("group");
        if (empty($startDate)) {
            $startDate = new DateTime();
            if (!$isVerbal) {
                $startDate = $startDate->sub(new DateInterval("P1M"));
            } else {
                $startDate = $startDate->sub(new DateInterval("P2D"));
            }
            
        }
        if (empty($endDate)) {
            $endDate = new DateTime();
            $endDate = $endDate->add(new DateInterval("P1D"));
        }
        
        $allDataObj = array();
        if (empty($groupId)) {
            $allServiceId = self::$db->getAllServiceId();
        } else {
            $allServiceId = self::$db->getServiceIdByHostId($groupId);
            if (empty($allServiceId)) {
                throw new Exception("No service registered in the report, or report group does not exist.");
            }
        }

        foreach ($allServiceId as $id) {
            $allDataObj[] = new PerfDataObject($id, [
                "startDate" => $startDate,
                "endDate" => $endDate,
                "isVerbal" => $isVerbal,
                "debugMode" => $request->get("debug")
            ]);
        }
        $printer = new PerfDataPrinter($allDataObj, [
            "isVerbal" => $isVerbal
        ]);
        if (!$request->get("debug")) {
            $response->headers->set("Content-type", "application/vnd.ms-excel");
            $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'report.xlsx');
            $response->headers->set("Content-Disposition", $disposition);
            $response->send();
            $printer->echoContent();
        }
        return $response;
    }
}
