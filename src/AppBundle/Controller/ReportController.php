<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\View\ExcelPrinter\AvailabilityPrinter;
use AppBundle\Model\Availability\AvailabilityFactory;
use DateTime;
use DateInterval;

class ReportController extends Controller{
    
    public function getAvailabilityReport(Request $request) {
        $response = new Response();
        $data = array();
        $debug = $request->query->get('debug');
        $month = $request->query->get('month');
        if (!$month) {
            $month = date("m");
        }
        $year = $request->query->get('year');
        if (!$year) {
            $year = date("Y");
        }
        $startDate = DateTime::createFromFormat("Y-m-d H:i:s", $year."-".$month."-"."01 00:00:00");
        $endDate = DateTime::createFromFormat("Y-m-d H:i:s", $year."-".$month."-"."01 00:00:00")->add(new DateInterval("P1M"))->sub(new DateInterval("P1D"));
        if ($endDate > new DateTime()) {
            $endDate = new DateTime();
        }
        $availabiltyModel = new AvailabilityFactory($startDate, $endDate);
        $serializedData = $availabiltyModel->jsonSerialize();
        $metaData = array(
            "debugMode" => $debug
        );
        $printer = new AvailabilityPrinter(array_merge($metaData, $serializedData));
        $printer->build();
        if (!$debug) {
            $response->headers->set("Content-type", "application/vnd.ms-excel");
            $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'report.xlsx');
            $response->headers->set("Content-Disposition", $disposition);
            $response->send();
            $printer->echoContent();
        }
        return $response;
    }
}
